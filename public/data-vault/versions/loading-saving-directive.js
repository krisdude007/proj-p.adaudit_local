"use strict";
// https://github.com/MandarinConLaBarba/angular-examples/blob/master/loading-indicator/index.html
versionApp.directive("loadingIndicator", function() {
    return {
        restrict : "A",
        template: "<div class='text-muted'>Loading...<i class='fa fa-refresh fa-spin fa-fw margin-bottom'></i></div>",
        link : function(scope, element, attrs) {
            scope.$on("loading-started", function(e) {
                element.css({"display" : ""});
            });
            scope.$on("loading-complete", function(e) {
                element.css({"display" : "none"});
            });
        }
    };
});
versionApp.directive("savingIndicator", function() {
    return {
        restrict : "A",
        template: "<div class='text-muted'>Saving...<i class='fa fa-refresh fa-spin fa-fw margin-bottom'></i></div>",
        link : function(scope, element, attrs) {
            scope.$on("saving-started", function(e) {
                element.css({"display" : ""});
            });
            scope.$on("saving-complete", function(e) {
                element.css({"display" : "none"});
            });
        }
    };
});