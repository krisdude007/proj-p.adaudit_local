"use strict";
var versionApp = angular.module('versionApp', ['ui.bootstrap','angucomplete-alt','ui.grid','ui.grid.selection','ui.grid.resizeColumns','ui.grid.expandable','ui.grid.saveState']);

// Decorate the table header text. 
// E.G.: int_repeat becomes INT REPEAT
versionApp.filter('tableHeader', function(){
    return function(item){
        return item.replace('_',' ').toUpperCase();
    }
});