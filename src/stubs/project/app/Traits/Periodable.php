<?php
    namespace App\Traits;

    trait Periodable {
        public static function scopeBeforePeriod($query, $now = false) {
            $qry = "((start_at AND (start_at < ?) ) OR (!start_at))";
            $now = $now?? new \Datetime();
            return $query->whereRaw($qry, [$now]);
        }

        public static function scopeAfterPeriod($query, $now = false) {
            $now = $now? $now : new \Datetime();
            $qry1 = "(start_at > ?)"; //rules for start_at to be active
            $qry2 = "(end_at > ?)"; //rules for end_at to be active
            $qry4 = "(start_at AND ({$qry1}) ) OR (!start_at AND end_at)";
            $qry5 = "(end_at AND ({$qry2}) ) OR (start_at AND !end_at)";//if only one start_at exits
            $qry5 = "((($qry4) OR ($qry5)) OR (!start_at AND !end_at))"; //if both period_from and period_to don't exist
            return $query->whereRaw($qry5, [$now, $now]);
        }

        public static function scopeEndAfter($query, $now = false) {
            $now = $now? $now : new \Datetime();
            $qry5 = "((end_at AND end_at > ? ) OR (!end_at))";//if only one start_at exits
            return $query->whereRaw($qry5, [$now]);
        }

        public function scopeInPeriod($query, $now = false) {
            $now = $now? $now : new \Datetime();
            $qry1 = "(start_at < ?)"; //rules for start_at to be active
            $qry2 = "(end_at > ?)"; //rules for end_at to be active
            $qry4 = "(start_at AND ({$qry1}) ) OR (!start_at AND end_at)"; //if only one end_at exits
            $qry5 = "(end_at AND ({$qry2}) ) OR (start_at AND !end_at)";//if only one start_at exits
            $qry5 = "((($qry4) AND ($qry5)) OR (!start_at AND !end_at))"; //if both period_from and period_to don't exist
            $now = $now?? new \Datetime();
            return $query->whereRaw($qry5, [$now, $now]);
        }

        public function scopeBetweenPeriod($query, $startAt, $endAt) {
            return $query
                ->afterPeriod($startAt)
                ->beforePeriod($endAt);
        }

        public function getInPeriodAttribute() {
            $now = new \DateTime();
            if ($start_atDT = $this->start_at) {
                if ($now < $start_atDT) {
                    return false;
                }
            }
            if ($end_atDT = $this->end_at) {
                if ($now > $end_atDT) {
                    return false;
                }
            }
            return true;
        }

        public function periodCollide($startC, $endC) {
            if(!$startC && !$endC) return true; //period is forever
            if(!$this->start_at && !$this->end_at) return true;//booking is forever

            if(!$this->start_at) { //booking start_at is NULL
                if($startC) { //period have start
                    if ($startC < $this->end_at)
                        return true;
                    return false;
                }
                //period have only end
                if ($endC <= $this->end_at)
                    return true;
                return false;

            }
            if(!$this->end_at) { //booking end_at is NULL
                if($startC) {//period have start
                    if($startC >= $this->start_at)
                        return true;
                    if(!$endC) //period do not have end
                        return true;
                    if($endC > $this->start_at) //period  have end
                        return true;
                    return false;
                }
                if($endC > $this->start_at)
                    return true;
                return false;
            }

            if(!$startC) { //period do not have start
                if($endC > $this->start_at)
                    return true;
                return false;
            }
            if(!$endC) { //period do not have end
                if($startC < $this->end_at)
                    return true;
                return false;
            }

            if($startC >= $this->start_at && $startC < $this->end_at) //startC in the Booking
                return true;

            if($endC > $this->start_at && $endC <= $this->end_at) //$endC in the Booking
                return true;

            if($startC < $this->start_at && $endC > $this->end_at) //booking is smaller than the period
                return true;

            return false;
        }

        public function scopePeriodCollide($query, $startC, $endC) {
            if(!$startC && !$endC)
                return $query; //period is forever
            return $query->where(function($qry) use ($startC, $endC) {
                $qry->where(function($qry2) {//booking is forever
                    $qry2->whereNull('start_at')->whereNull('end_at');
                })->orWhere(function($qry2) use ($startC, $endC) { //booking start_at is NULL
                    $qry2->whereNull('start_at');
                    if($startC) {//period have start
                        $qry2->where('end_at', '>', $startC);
                    } else { //period have only end
                        $qry2->where('end_at', '>=', $endC);
                    }
                })->orWhere(function($qry2) use ($startC, $endC) { //booking end_at is NULL
                    $qry2->whereNull('end_at');
                    if($startC) {//period have start
                        $qry2->where(function($qry3) use ($startC, $endC) {
                            $qry3->where('start_at', '<=', $startC);
                            if($endC) { //period  have end
                                $qry3->orWhere('start_at', '<', $endC);
                            } else {//period do not have end
                                $qry3->orWhere('id', '!=', 0);
                            }
                        });
                    } else { //period do not have start
                        $qry2->where('start_at', '<', $endC);
                    }
                })->orWhere(function($qry2) use ($startC, $endC) {
                    $qry2->whereNotNull('start_at')->whereNotNull('end_at');
                    if($startC) {
                        if($endC) {
                            $qry2->where(function($qry3) use ($startC, $endC){
                                $qry3->where(function($qry4) use ($startC, $endC) { //startC in the Booking
                                    $qry4->where('start_at', '<=', $startC)
                                        ->where('end_at', '>', $startC);
                                })->orWhere(function($qry4) use ($startC, $endC) { //endC in the Booking
                                    $qry4->where('start_at', '<', $endC)
                                        ->where('end_at', '>=', $endC);
                                })->orWhere(function($qry4) use ($startC, $endC) {  //booking is smaller than the period
                                    $qry4->where('start_at', '>', $startC)
                                        ->where('end_at', '<', $endC);
                                });
                            });
                        } else {//period do not have end
                            $qry2->where('end_at', '>', $startC);
                        }
                    } else { //period do not have start
                        $qry2->where('start_at', '<', $endC);
                    }
                });
            });
        }
    }
