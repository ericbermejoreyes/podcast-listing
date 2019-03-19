<div id="gui">
    <button id="signout-button">Sign Out</button>
    <h1>Podcast Listing Generator</h1>
    <form action="POST" name="podcast-request" id="podcast-request">
        <div class="capsule">
            <div class="capsule-field">
                <select name="genre_id" title="Select the podcast Genre/Category you want to generate" required>
                    <option disabled selected value="">- Choose Category -</option>
                    <option value="-1">All Categories</option>
                    @foreach ($genreGroup as $groupName => $genres)
                        <optgroup label="{{ $groupName }}:">
                            @foreach ($genres as $genre)
                                    <option value="{{ $genre['id'] }}">{{ $genre['name'] }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <select name="safe_mode" id="" title="Whether or not to exclude podcasts/episodes with explicit language">
                    <option value="0" selected>Safe Mode Disabled</option>
                    <option value="1">Safe Mode Enabled</option>
                </select>
                <input type="hidden" name="genre_name"/>
            </div>
            <input type="submit" value="Generate" class="capsule-button"/>
        </div>
    </form>
    <img src="{{ asset('img/load.gif') }}" alt="" class="mini-load" style="display: none">
    <div id="error-message" style="display: none">
        <span><span class="bold">Operation stopped!</span> Failed to generate data for category: <span id="failed-category-name"></span></span>
    </div>
    <div id="url-output" style="display: none;">
        <p>Generate complete! Spreadsheet has been updated:</p>
        <a id="url" href="https://docs.google.com/spreadsheets/d/{{ explode('::', $_google['spreadsheetId'])[0] }}" target="_blank">
            https://docs.google.com/spreadsheets/d/{{ explode('::', $_google['spreadsheetId'])[0] }}
        </a>
    </div>
</div>