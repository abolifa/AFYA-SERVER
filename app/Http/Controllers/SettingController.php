<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController
{
    public function getAbout(): JsonResponse
    {
        $about = Setting::query()
            ->select([
                'about_title',
                'about_content',
            ])
            ->latest()
            ->first();
        return response()->json($about);
    }

    public function getPrivacyPolicy(): JsonResponse
    {
        $privacyPolicy = Setting::query()
            ->select([
                'privacy_policy_title',
                'privacy_policy_content',
            ])
            ->latest()
            ->first();
        return response()->json($privacyPolicy);
    }

    public function getTerms(): JsonResponse
    {
        $terms = Setting::query()
            ->select([
                'terms_of_service_title',
                'terms_of_service_content',
            ])
            ->latest()
            ->first();
        return response()->json($terms);
    }

    public function getFaq(): JsonResponse
    {
        $faq = Setting::query()
            ->select([
                'faq_title',
                'faq_content',
                'faq',
            ])
            ->latest()
            ->first();
        return response()->json($faq);
    }

    public function getContact(): JsonResponse
    {
        $contact = Setting::query()
            ->select([
                'contact',
            ])
            ->latest()
            ->first();
        return response()->json($contact);
    }
}
