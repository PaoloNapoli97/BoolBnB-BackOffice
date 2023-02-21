<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.23.0/maps/maps-web.min.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/6.x/6.23.0/services/services-web.min.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/cdn/plugins/SearchBox/3.2.0//SearchBox-web.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/search-markers/search-marker.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/search/search-results-parser.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/search-markers/search-markers-manager.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/info-hint.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/mobile-or-tablet.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/search/results-manager.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/search/side-panel.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/search/dom-helpers.js"></script>
<script src="https://api.tomtom.com/maps-sdk-for-web/6.x/6.23.0//examples/pages/examples/assets/js/formatters.js"></script>

<script>
    tt.setProductInfo('Codepen Examples', '${analytics.productVersion}');
var map = tt.map({
    key: 'S7Di8WQbB2pqxqTH8RYmhO63cZwgtNgp',
    container: 'map',
    center: [15.4, 53.0],
    zoom: 3,
    dragPan: !window.isMobileOrTablet()
});
var infoHint = new InfoHint('info', 'bottom-center', 5000).addTo(document.getElementById('map'));
var errorHint = new InfoHint('error', 'bottom-center', 5000).addTo(document.getElementById('map'));
// Options for the fuzzySearch service
var searchOptions = {
    key: 'S7Di8WQbB2pqxqTH8RYmhO63cZwgtNgp',
    language: 'en-GB',
    limit: 5
};
// Options for the autocomplete service
var autocompleteOptions = {
    key: 'S7Di8WQbB2pqxqTH8RYmhO63cZwgtNgp',
    language: 'en-GB'
};
var searchBoxOptions = {
    minNumberOfCharacters: 0,
    searchOptions: searchOptions,
    autocompleteOptions: autocompleteOptions,
    distanceFromPoint: [15.4, 53.0]
};
var ttSearchBox = new tt.plugins.SearchBox(tt.services, searchBoxOptions);
document.querySelector('.tt-side-panel__header').appendChild(ttSearchBox.getSearchBoxHTML());
var state = {
    previousOptions: {
        query: null,
        center: null
    },
    callbackId: null,
    userLocation: null
};
map.addControl(new tt.FullscreenControl({container: document.querySelector('body')}));
map.addControl(new tt.NavigationControl());
new SidePanel('.tt-side-panel', map);
var geolocateControl = new tt.GeolocateControl({
    positionOptions: {
        enableHighAccuracy: false
    }
});
geolocateControl.on('geolocate', function(event) {
    var coordinates = event.coords;
    state.userLocation = [coordinates.longitude, coordinates.latitude];
    ttSearchBox.updateOptions(Object.assign({}, ttSearchBox.getOptions(), {
        distanceFromPoint: state.userLocation
    }));
});
map.addControl(geolocateControl);
var resultsManager = new ResultsManager();
var searchMarkersManager = new SearchMarkersManager(map);
map.on('load', handleMapEvent);
map.on('moveend', handleMapEvent);
ttSearchBox.on('tomtom.searchbox.resultscleared', handleResultsCleared);
ttSearchBox.on('tomtom.searchbox.resultsfound', handleResultsFound);
ttSearchBox.on('tomtom.searchbox.resultfocused', handleResultSelection);
ttSearchBox.on('tomtom.searchbox.resultselected', handleResultSelection);
function handleMapEvent() {
    // Update search options to provide geobiasing based on current map center
    var oldSearchOptions = ttSearchBox.getOptions().searchOptions;
    var oldautocompleteOptions = ttSearchBox.getOptions().autocompleteOptions;
    var newSearchOptions = Object.assign({}, oldSearchOptions, { center: map.getCenter() });
    var newAutocompleteOptions = Object.assign({}, oldautocompleteOptions, { center: map.getCenter() });
    ttSearchBox.updateOptions(Object.assign({}, searchBoxOptions, {
        placeholder: 'Query e.g. Washington',
        searchOptions: newSearchOptions,
        autocompleteOptions: newAutocompleteOptions,
        distanceFromPoint: state.userLocation
    }));
}
function handleResultsCleared() {
    searchMarkersManager.clear();
    resultsManager.clear();
}
function handleResultsFound(event) {
    // Display fuzzySearch results if request was triggered by pressing enter
    if (event.data.results && event.data.results.fuzzySearch && event.data.metadata.triggeredBy === 'submit') {
        var results = event.data.results.fuzzySearch.results;
        if (results.length === 0) {
            handleNoResults();
        }
        searchMarkersManager.draw(results);
        resultsManager.success();
        fillResultsList(results);
        fitToViewport(results);
    }
    if (event.data.errors) {
        errorHint.setMessage('There was an error returned by the service.');
    }
}
function handleResultSelection(event) {
    if (isFuzzySearchResult(event)) {
        // Display selected result on the map
        var result = event.data.result;
        resultsManager.success();
        searchMarkersManager.draw([result]);
        fillResultsList([result]);
        searchMarkersManager.openPopup(result.id);
        fitToViewport(result);
        state.callbackId = null;
        infoHint.hide();
    } else if (stateChangedSinceLastCall(event)) {
        var currentCallbackId = Math.random().toString(36).substring(2, 9);
        state.callbackId = currentCallbackId;
        // Make fuzzySearch call with selected autocomplete result as filter
        handleFuzzyCallForSegment(event, currentCallbackId);
    }
}
function isFuzzySearchResult(event) {
    return !('matches' in event.data.result);
}
function stateChangedSinceLastCall(event) {
    return Object.keys(searchMarkersManager.getMarkers()).length === 0 || !(
        state.previousOptions.query === event.data.result.value &&
        state.previousOptions.center.toString() === map.getCenter().toString());
}
function getBounds(data) {
    var southWest;
    var northEast;
    if (data.viewport) {
        southWest = [data.viewport.topLeftPoint.lng, data.viewport.btmRightPoint.lat];
        northEast = [data.viewport.btmRightPoint.lng, data.viewport.topLeftPoint.lat];
    }
    return [southWest, northEast];
}
function fitToViewport(markerData) {
    if (!markerData || markerData instanceof Array && !markerData.length) {
        return;
    }
    var bounds = new tt.LngLatBounds();
    if (markerData instanceof Array) {
        markerData.forEach(function(marker) {
            bounds.extend(getBounds(marker));
        });
    } else {
        bounds.extend(getBounds(markerData));
    }
    map.fitBounds(bounds, { padding: 100, linear: true });
}
function handleFuzzyCallForSegment(event, currentCallbackId) {
    var query = ttSearchBox.getValue();
    var segmentType = event.data.result.type;
    var commonOptions = Object.assign({}, searchOptions, {
        query: query,
        limit: 15,
        center: map.getCenter(),
        typeahead: true,
        language: 'en-GB'
    });
    var filter;
    if (segmentType === 'category') {
        filter = { categorySet: event.data.result.id };
    }
    if (segmentType === 'brand') {
        filter = { brandSet: event.data.result.value };
    }
    var options = Object.assign({}, commonOptions, filter);
    infoHint.setMessage('Loading results...');
    errorHint.hide();
    resultsManager.loading();
    tt.services.fuzzySearch(options)
        .then(function(response) {
            if (state.callbackId !== currentCallbackId) {
                return;
            }
            if (response.results.length === 0) {
                handleNoResults();
                return;
            }
            resultsManager.success();
            searchMarkersManager.draw(response.results);
            fillResultsList(response.results);
            map.once('moveend', function() {
                state.previousOptions = {
                    query: query,
                    center: map.getCenter()
                };
            });
            fitToViewport(response.results);
        })
        .catch(function(error) {
            if (error.data && error.data.errorText) {
                errorHint.setMessage(error.data.errorText);
            }
            resultsManager.resultsNotFound();
        })
        .finally(function() {
            infoHint.hide();
        });
}
function handleNoResults() {
    resultsManager.clear();
    resultsManager.resultsNotFound();
    searchMarkersManager.clear();
    infoHint.setMessage(
        'No results for "' +
        ttSearchBox.getValue() +
        '" found nearby. Try changing the viewport.'
    );
}
function fillResultsList(results) {
    resultsManager.clear();
    var resultList = DomHelpers.createResultList();
    results.forEach(function(result) {
        var distance = state.userLocation ? SearchResultsParser.getResultDistance(result) : undefined;
        var addressLines = SearchResultsParser.getAddressLines(result);
        var searchResult = this.DomHelpers.createSearchResult(
            addressLines[0],
            addressLines[1],
            distance ? Formatters.formatAsMetricDistance(distance) : ''
        );
        var resultItem = DomHelpers.createResultItem();
        resultItem.appendChild(searchResult);
        resultItem.setAttribute('data-id', result.id);
        resultItem.onclick = function(event) {
            var id = event.currentTarget.getAttribute('data-id');
            searchMarkersManager.openPopup(id);
            searchMarkersManager.jumpToMarker(id);
        };
        resultList.appendChild(resultItem);
    });
    resultsManager.append(resultList);
}

const ttSidePanel = document.querySelector('.tt-side-panel__close-button');
ttSidePanel.click();
setTimeout(() => {
    ttSidePanel.click();
}, 100);

const address = document.querySelector('.tt-search-box-input');
address.setAttribute('id', 'full_address');
address.setAttribute('name', 'full_address');

</script>
