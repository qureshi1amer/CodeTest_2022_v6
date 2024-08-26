<?php

namespace DTApi\Services\Notifications;

use DTApi\Helpers\SendSMSHelper;
use Illuminate\Support\Facades\Log;

class SMSNotification
{
    public function send($translators, $job)
    {
        $jobPosterMeta = UserMeta::where('user_id', $job->user_id)->first();
        $date = date('d.m.Y', strtotime($job->due));
        $time = date('H:i', strtotime($job->due));
        $duration = $this->convertToHoursMins($job->duration);
        $jobId = $job->id;
        $city = $job->city ? $job->city : $jobPosterMeta->city;

        $phoneJobMessageTemplate = trans('sms.phone_job', ['date' => $date, 'time' => $time, 'duration' => $duration, 'jobId' => $jobId]);
        $physicalJobMessageTemplate = trans('sms.physical_job', ['date' => $date, 'time' => $time, 'town' => $city, 'duration' => $duration, 'jobId' => $jobId]);

        $message = ($job->customer_physical_type == 'yes' && $job->customer_phone_type == 'no')
                ? $physicalJobMessageTemplate : (($job->customer_physical_type == 'no' && $job->customer_phone_type == 'yes')
                ? $phoneJobMessageTemplate : (($job->customer_physical_type == 'yes' && $job->customer_phone_type == 'yes')
                ? $phoneJobMessageTemplate : ''
                ));

        Log::info($message);

        foreach ($translators as $translator) {
            /** Logic can be moved to send sms */
            $status = SendSMSHelper::send(env('SMS_NUMBER'), $translator->mobile, $message);
            Log::info('Send SMS to ' . $translator->email . ' (' . $translator->mobile . '), status: ' . print_r($status, true));
        }

        return count($translators);
    }

    function sendSms()
    {


    }

    private function convertToHoursMins($time )
    {
        if ($time < 60) {
            return $time . 'min';
        } else if ($time == 60) {
            return '1h';
        }

        $hours = floor($time / 60);
        $minutes = ($time % 60);

        return sprintf($format, $hours, $minutes);
    }
}
