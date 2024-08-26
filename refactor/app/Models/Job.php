<?php

namespace DTApi\Models;

/** Need sepecific imports here  */

class Job extends Model
{

    /** NOTE::  All the other methods above it
     * we can scope the fiters instead of applying it manally so we can reuse them plus it
     *cleaner
     */
    public function scopeWithFeedback($query, $feedback)
    {
        if ($feedback != 'false') {
            return $query->where('ignore_feedback', '0')
                ->whereHas('feedback', function ($q) {
                    $q->where('rating', '<=', '3');
                });
        }
    }


    public function scopeByIds($query, $ids)
    {
        if (!empty($ids)) {
            return is_array($ids) ? $query->whereIn('id', $ids) : $query->where('id', $ids);
        }
    }

    // Scope for filtering by languages
    public function scopeByLanguages($query, $languages)
    {
        if (!empty($languages)) {
            return $query->whereIn('from_language_id', $languages);
        }
    }

    // Scope for filtering by status
    public function scopeByStatus($query, $statuses)
    {
        if (!empty($statuses)) {
            return $query->whereIn('status', $statuses);
        }
    }


    public function scopeByTime($query, $type, $from, $to)
    {
        if (!empty($from)) {
            $query->where($type, '>=', $from);
        }
        if (!empty($to)) {
            $query->where($type, '<=', $to . ' 23:59:00');
        }
        return $query->orderBy($type, 'desc');
    }


    public function scopeByJobType($query, $jobTypes)
    {
        if (!empty($jobTypes)) {
            return $query->whereIn('job_type', $jobTypes);
        }
    }


    public function scopeByPhysicalPhone($query, $physical, $phone)
    {
        if (!is_null($physical)) {
            $query->where('customer_physical_type', $physical)->where('ignore_physical', 0);
        }

        if (!is_null($phone)) {
            $query->where('customer_phone_type', $phone)->where('ignore_physical_phone', 0);
        }

        return $query;
    }


    public function scopeFlagged($query, $flagged)
    {
        if (!is_null($flagged)) {
            return $query->where('flagged', $flagged)->where('ignore_flagged', 0);
        }
    }


    public function scopeByDistance($query, $distance)
    {
        if ($distance == 'empty') {
            return $query->whereDoesntHave('distance');
        }
    }


    public function scopeByConsumerType($query, $consumerType)
    {
        if (!empty($consumerType)) {
            return $query->whereHas('user.userMeta', function ($q) use ($consumerType) {
                $q->where('consumer_type', $consumerType);
            });
        }
    }

    public function scopeBySalary($query, $salary)
    {
        if ($salary == 'yes') {
            return $query->whereDoesntHave('user.salaries');
        }
    }
    public function scopeForCustomer($query)
    {
        return $query->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback')
            ->whereIn('status', ['pending', 'assigned', 'started'])
            ->orderBy('due', 'asc');
    }

    public function scopeForTranslator($query, $translatorId)
    {
        return $query->whereHas('translatorJobRel', function ($q) use ($translatorId) {
            $q->where('user_id', $translatorId)
                ->where('cancel_at', null);
        });
    }

    public function scopeNewTranslatorJobs($query, $translatorId)
    {
        return $query->forTranslator($translatorId)->get()->pluck('jobs')->flatten();
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'from_language_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'job_id');
    }

    public function distance()
    {
        return $this->hasOne(Distance::class, 'job_id');
    }
    public function scopeWithSessionTime($query)
    {
        return $query->whereRaw('TIMESTAMPDIFF(MINUTE, ' . DB::raw('session_time'), DB::raw('NOW()')) . ' >= duration');
    }

    private function mapJobFor()
    {
        $jobFor = [];
        if ($this->gender) {
            $jobFor[] = $this->gender === 'male' ? 'Man' : 'Kvinna';
        }
        if ($this->certified) {
            $certifiedMap = [
                'both' => ['normal', 'certified'],
                'yes' => 'certified',
                'law' => 'certified_in_law',
                'health' => 'certified_in_helth',
                'n_law' => 'normal_certified_in_law',
                'n_health' => 'normal_certified_in_helth',
            ];
            $jobFor = array_merge($jobFor, is_array($certifiedMap[$this->certified]) ? $certifiedMap[$this->certified] : [$certifiedMap[$this->certified]]);
        }
        return $jobFor;
    }
}
