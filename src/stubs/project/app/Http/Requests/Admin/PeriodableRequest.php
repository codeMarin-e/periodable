<?php
namespace App\Http\Requests\Admin;

class PeriodableRequest {

    public static function validation_rules($lang_prefix = null, $periodable_bag = false) {
        $translations = trans('admin/periodable/validation');
        $langs = isset($lang_prefix)?
            transOrOther($lang_prefix, 'admin/periodable/validation', array_keys($translations)) : $translations;
        $periodable_bag = $periodable_bag? $periodable_bag : 'period';
        return [
            $periodable_bag.'.start_at.date' => ['nullable',  function($attribute, $value, $fail) use ($langs) {
                if(!($dt = \DateTime::createFromFormat('d.m.Y', $value))) {
                    return $fail( $langs['start_at_not_correct'] );
                }
            }],
            $periodable_bag.'.start_at.hour' => [ function($attribute, $value, $fail) use ($langs) {
                $value = (int)$value;
                if($value < 0 || $value > 23) {
                    return $fail( $langs['start_at_hour_not_correct'] );
                }
            }],
            $periodable_bag.'.start_at.minutes' => [ function($attribute, $value, $fail) use ($langs) {
                $value = (int)$value;
                if($value < 0 || $value > 59) {
                    return $fail( $langs['start_at_minutes_not_correct'] );
                }
            }],
            $periodable_bag.'.end_at.date' => ['nullable', function($attribute, $value, $fail) use ($langs) {
                if(!($dt = \DateTime::createFromFormat('d.m.Y', $value))) {
                    return $fail( $langs['end_at_not_correct'] );
                }
            }],
            $periodable_bag.'.end_at.hour' => [ function($attribute, $value, $fail) use ($langs) {
                $value = (int)$value;
                if($value < 0 || $value > 23) {
                    return $fail( $langs['end_at_hour_not_correct'] );
                }
            }],
            $periodable_bag.'.end_at.minutes' => [ function($attribute, $value, $fail) use ($langs) {
                $value = (int)$value;
                if($value < 0 || $value > 59) {
                    return $fail( $langs['end_at_minutes_not_correct'] );
                }
            }],
        ];
    }

    public static function validateData(&$validatedData, $periodable_bag = false) {
        $periodable_bag = $periodable_bag? $periodable_bag : 'period';
        $validatePeriodArr = $validatedData[$periodable_bag]['start_at'];
        $periodFrom = "{$validatePeriodArr['date']} {$validatePeriodArr['hour']}:{$validatePeriodArr['minutes']}";
        $validatePeriodArr = $validatedData['period']['end_at'];
        $periodTo = "{$validatePeriodArr['date']} {$validatePeriodArr['hour']}:{$validatePeriodArr['minutes']}";
        if(!($validatedData['start_at'] = \DateTime::createFromFormat('d.m.Y H:i', $periodFrom))) {
            $validatedData['start_at'] = null;
        }
        if(!($validatedData['end_at'] = \DateTime::createFromFormat('d.m.Y H:i', $periodTo))) {
            $validatedData['end_at'] = null;
        }
        if($validatedData['start_at'] && $validatedData['end_at'] && $validatedData['start_at'] > $validatedData['end_at']) {
            $buf = clone $validatedData['start_at'];
            $validatedData['start_at'] = clone $validatedData['end_at'];
            $validatedData['end_at'] = clone $buf;
        }
        unset($validatedData[$periodable_bag]);
    }
}
