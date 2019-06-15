function ApiSpreadsheet(callback)  {
    // Array of API discovery doc URLs for APIs used by the quickstart
    const DISCOVERY_DOCS = ["https://sheets.googleapis.com/$discovery/rest?version=v4"];

    // Authorization scopes required by the API; multiple scopes can be
    // included, separated by spaces.
    const SCOPES = "https://www.googleapis.com/auth/spreadsheets";

    let self = {
        getInfo(spreadsheetId) {
            let params = {
                spreadsheetId: spreadsheetId
            };

            let request = gapi.client.sheets.spreadsheets.get(params);

            return getResponse(request);
        },
        create(title) {
           let requestBody = {
               properties: {
                   title: title
               }
           };

           let request = getClient().spreadsheets.create({}, requestBody);

           return getResponse(request);
        },
        newSheet(spreadsheetId, title) {
            let params = {
                spreadsheetId: spreadsheetId
            };
            let requestBody = {
                requests: [
                    {
                        addSheet: {
                            properties: {
                                title: title
                            }
                        }
                    }
                ]
            };
            let request = getClient().spreadsheets.batchUpdate(params, requestBody);

            return getResponse(request, function (result) {
                return result.replies[0].addSheet;
            });
        },
        signIn() {
            return gapi.auth2.getAuthInstance().signIn();
        },
        signOut() {
            gapi.auth2.getAuthInstance().signOut();
            window.location.reload();
        },
        isSignedIn() {
            return gapi.auth2.getAuthInstance().isSignedIn.get()
        }
    };

    /**
     *  Called when the signed in status changes, to update the UI
     *  appropriately. After a sign-in, the API is called.
     */
    function updateSigninStatus(isSignedIn) {
        if (callback) callback({signedIn: isSignedIn});
    }

    function getResponse(request, callback) {
        return new Promise(function (resolve, reject) {
            request.then(function(response) {
                if (callback) {
                    resolve(callback(response.result));
                } else {
                    resolve(response.result);
                }
            }, function(reason) {
                console.error('error: ' + reason.result.error.message);
                reject(reason.result.error.message);
            });
        });
    }

    /**
     *  Initializes the API client library and sets up sign-in state
     *  listeners.
     */
    function initClient() {
        gapi.client.init({
            apiKey: G_API_KEY,
            clientId: G_CLIENT_ID,
            discoveryDocs: DISCOVERY_DOCS,
            scope: SCOPES
        }).then(function () {
            // Listen for sign-in state changes.
            gapi.auth2.getAuthInstance().isSignedIn.listen(function (isSignedIn) {
               // do something when a user signs in
            });

            // Handle the initial sign-in state.
            updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());

        }, function(error) {
            console.error(JSON.stringify(error, null, 2));
        });
    }

    function getClient() {
        return gapi.client.sheets;
    }

    gapi.load('client:auth2', initClient);

    return self;
}