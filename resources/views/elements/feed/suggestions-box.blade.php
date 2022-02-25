<div class="suggestions-box border rounded-lg px-2 py-4">
    <div class="d-flex justify-content-between suggestions-header mb-3 px-1">
        <h5 class="card-title pl-2 mb-0">{{__('Suggestions')}}</h5>
        <div class="d-flex">
            <div class="d-flex">
            </div>
            <div class="d-flex">
                <span class="mr-2 mr-xl-3 d-none d-lg-block pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Free account only')}}" onclick="SuggestionsSlider.loadSuggestions({'free':true});">
                    @include('elements.icon',['icon'=>'pricetag-outline','variant'=>'medium','centered'=>false])
                </span>
                <span class="mr-2 mr-xl-3 pointer-cursor" data-toggle="tooltip" data-placement="top" title="{{__('Refresh suggestions')}}" onclick="SuggestionsSlider.loadSuggestions()">
                   @include('elements.icon',['icon'=>'refresh','variant'=>'medium','centered'=>false])
                </span>
            </div>
        </div>
    </div>
    @include('elements.feed.suggestions-wrapper',['profiles'=>$profiles])
</div>
