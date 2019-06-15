function Sheet(spreadsheetId, properties, existing = false) {
    const HEADERS = ['Show', 'Host', 'Email', 'Description', 'Note'];

    let self = {
        update(range, values) {
            let params = {
                spreadsheetId: spreadsheetId,
                valueInputOption: 'RAW',
                range: makeRange(range)
            };
            let valueRange = {
                majorDimension: 'ROWS',
                values: values,
                range: makeRange(range)
            };

            let request = getClient().spreadsheets.values.update(params, valueRange);

            return getResponse(request);
        },
        clear(range) {
            let params = {
                spreadsheetId: spreadsheetId,
                range: makeRange(range)
            };

            let request = getClient().spreadsheets.values.clear(params, {});

            return getResponse(request);
        },
        delete() {
            let params = {
                spreadsheetId: spreadsheetId
            };

            let requestBody = {
                requests: [
                    {
                        deleteSheet: {
                            sheetId: self.properties.sheetId
                        }
                    }
                ]
            };

            let request = getClient().spreadsheets.batchUpdate(params, requestBody);

            return getResponse(request);
        },
        fixCellSize() {
            let params = {
                spreadsheetId: spreadsheetId
            };
            let requestBody = {
                requests: [
                    {
                        autoResizeDimensions: {
                            dimensions: {
                                sheetId: self.properties.sheetId,
                                dimension: 'COLUMNS',
                                startIndex: 0
                            }
                        }
                    },
                    {
                        updateDimensionProperties: {
                            range: {
                                sheetId: self.properties.sheetId,
                                dimension: "COLUMNS",
                                startIndex: 1,
                                endIndex: 4
                            },
                            properties: {
                                pixelSize: 400
                            },
                            fields: "pixelSize"
                        }
                    },
                    {
                        updateDimensionProperties: {
                            range: {
                                sheetId: self.properties.sheetId,
                                dimension: "COLUMNS",
                                startIndex: 3
                            },
                            properties: {
                                pixelSize: 800
                            },
                            fields: "pixelSize"
                        }
                    }
                ]
            };

            let request = getClient().spreadsheets.batchUpdate(params, requestBody);

            return getResponse(request);
        }
    };

    Object.assign(self, properties);

    // return a Promise containing the response of the request
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

    function initHeaders() {
        self
            .update('A1:E1', [HEADERS])
            .then(function () {
                let params = {
                    spreadsheetId: spreadsheetId
                };
                let requestBody = {
                    requests: [
                        {
                            repeatCell: {
                                range: {
                                    sheetId: self.properties.sheetId,
                                    startRowIndex: 0,
                                    endRowIndex: 1
                                },
                                cell: {
                                    userEnteredFormat: {
                                        horizontalAlignment: "CENTER",
                                        textFormat: {
                                            fontSize: 16,
                                            bold: true
                                        }
                                    }
                                },
                                fields: "userEnteredFormat(textFormat,horizontalAlignment)"
                            }
                        },
                        {
                            updateSheetProperties: {
                                properties: {
                                    sheetId: self.properties.sheetId,
                                    gridProperties: {
                                        frozenRowCount: 1
                                    }
                                },
                                fields: "gridProperties.frozenRowCount"
                            }
                        }
                    ]
                };
                let request = getClient().spreadsheets.batchUpdate(params, requestBody);
                return getResponse(request);
            });
    }

    function makeRange(range) {
        return self.properties.title + '!' + range;
    }

    if (!existing) {
        initHeaders();
    }

    function getClient() {
        return gapi.client.sheets;
    }

    return self;
}