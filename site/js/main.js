"use strict";
$(document).ready(function() {
    // проверяем при инициализации страницы
    if(typeof($('.column-searcher-selects .region').val()) !== 'undefined'){
        $.ajax({
            type: 'POST',
            url: '/ajax/',
            data: {ajax: true, regionID: $('.column-searcher-selects .region').val(), method: 'getcitiesbyregion'}
        }).done(function(data){
            $('.column-searcher-selects .city').html(data);
        });
    }
    // делаем селект по городам и областям
    $('.column-searcher-selects .region').on('change', function(){
        $.ajax({
            type: 'POST',
            url: '/ajax/',
            data: {ajax: true, regionID: $('.column-searcher-selects .region').val(), method: 'getcitiesbyregion'}
        }).done(function(data){
            $('.column-searcher-selects .city').html(data);
        });
    });
});