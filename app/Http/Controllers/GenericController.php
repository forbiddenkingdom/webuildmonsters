<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveNewContactMessageRequest;
use App\Model\ContactMessage;
use App\Model\Country;
use App\Model\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GenericController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function countries()
    {
        // find taxes for all countries
        $allCountriesAppliedTaxes = Tax::query()
            ->select('taxes.*')
            ->join('country_taxes', 'taxes.id', '=', 'country_taxes.tax_id')
            ->join('countries', 'country_taxes.country_id', '=', 'countries.id')
            ->where('countries.name', '=', 'All')->get();

        $countries = Country::query()->where('name', '!=', 'All')->with(['taxes'])->get();
        if(count($allCountriesAppliedTaxes)){
            foreach ($countries as $country){
                foreach ($allCountriesAppliedTaxes as $appliedTax){
                    $country->taxes->add($appliedTax);
                }
            }
        }
        return response()->json([
            'countries'=> $countries,
        ]);
    }

    /**
     * Sets user locale.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLanguage(Request $request)
    {
        $user = Auth::user();
        $user->settings = collect(array_merge($user->settings->toArray(), ['locale'=>$request->route('locale')]));
        $user->save();

        // Resetting cached translation files ( for frontend )
        App::setLocale(Auth::user()->settings['locale']);
        $langPath = resource_path('lang/'.Auth::user()->settings['locale']);
        if (env('APP_ENV') == 'production') {
            Cache::forget('translations');
            Cache::rememberForever('translations', function () use ($langPath) {
                return file_get_contents($langPath.'.json');
            });
        } else {
            Cache::forget('translations');
            Cache::remember('translations', 5, function () use ($langPath) {
                return file_get_contents($langPath.'.json');
            });
        }
        return redirect()->back();
    }

    public function contact(Request $request){
        return view('pages.contact', []);
    }

    public function sendContactMessage(SaveNewContactMessageRequest $request){

        ContactMessage::create([
            'email' => $request->get('email'),
            'subject' => $request->get('subject'),
            'message' => $request->get('message'),
        ]);

        return back()->with('success', __('Message sent.'));
    }

    public function resendConfirmationEmail(){
        $user = Auth::user();
        $user->sendEmailVerificationNotification();
        return response()->json(['success' => true, 'message' => __('Verification email sent successfully.')]);
    }

}
