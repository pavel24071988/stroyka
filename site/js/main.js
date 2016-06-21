"use strict";
$(document).ready(function() {
    // проверяем при инициализации страницы
    if(typeof($('.column-searcher-selects .region').val()) !== 'undefined'){
        $.ajax({
            type: 'POST',
            url: '/ajax/',
            data: {ajax: true, regionID: $('.column-searcher-selects .region').val(), method: 'getcitiesbyregion'}
        }).done(function(data){
            $('.column-searcher-selects .city option').each(function(){
                if($( this ).attr('selected') === 'selected') data = '<option value="'+ $( this ).val() +'">'+ $( this ).text() +'</option>' + data;
            });
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
    // избранное портфолио на странице пользователя - тут тоже своя динамика
    $('.portfo-holder .portfo-item').on('click', function(){
        var objectID = $( this ).attr('data-objectid');
        $.ajax({
            type: 'POST',
            url: '/ajax/',
            data: {ajax: true, objectID: objectID, method: 'getphotoesforobject'}
        }).done(function(data){
            var imgs_str = '',
                data = $.parseJSON(data);
            for(var i in data.imgs){
                imgs_str += '<a class="portfo-thumb" href="#"><img data-imgID="'+ data.imgs[i].id +'" width="100px" height="100px" src="/images/objects/'+ objectID +'/'+ data.imgs[i].src +'"></a>';
            }
            $('#portfo .big-portfo-photo img').attr('src', '/images/objects/'+ objectID +'/'+ data.imgs[0].src).attr('data-imgID', data.imgs[0].id);
            $('#portfo .portfo-thumbs-holder').html(imgs_str);
            if(data.data.finished !== null) $('#portfo .portfo-info p').eq(0).html('<b>Год сдачи:</b> '+ (data.data.finished).split('-')[0]);
            $('#portfo .portfo-info p').eq(1).html('<b>Срок работы:</b> '+ data.data.term +' месяца');
            $('#portfo .portfo-info p').eq(2).html('<b>Стоимость:</b> '+ data.data.amount +' рублей');
            console.log($.parseJSON(data));
        });
    });
    // кнопки слайдера фоток объекта
    $('#portfo .portfo-nav').on('click', function(){
        var bigImg = $( this ).prevAll('img');
        var minHref = $('#portfo .portfo-thumbs-holder img[data-imgID="'+ bigImg.attr('data-imgID') +'"]');
        var firstA = $('#portfo .portfo-thumbs-holder a:first');
        var lastA = $('#portfo .portfo-thumbs-holder a:last');
        if($( this ).hasClass('left')){
            var prevA = minHref.parent().prev();
            if(prevA.hasClass('portfo-thumb'))
                bigImg.attr('data-imgID', prevA.find('img').attr('data-imgID')).attr('src', prevA.find('img').attr('src'));
            else
                bigImg.attr('data-imgID', lastA.find('img').attr('data-imgID')).attr('src', lastA.find('img').attr('src'));
        }else if($( this ).hasClass('right')){
            var nextA = minHref.parent().next();
            if(nextA.hasClass('portfo-thumb'))
                bigImg.attr('data-imgID', nextA.find('img').attr('data-imgID')).attr('src', nextA.find('img').attr('src'));
            else
                bigImg.attr('data-imgID', firstA.find('img').attr('data-imgID')).attr('src', firstA.find('img').attr('src'));
        }
        return false;
    });
    // нажатия на маленькие изображения слайдера
    $('#portfo').on('click', '.portfo-thumbs-holder a', function(){
        $('#portfo .big-portfo-photo').find('img').attr('src', $( this ).find('img').attr('src')).attr('data-imgID', $( this ).find('img').attr('data-imgID'));
        return false;
    });
    // сделать фотографию основной
    $('#portfo .make-main-holder a').on('click', function(){
        var imgID = $( this ).parents('#portfo').find('.big-portfo-photo img').attr('data-imgID');
        $.ajax({
            type: 'POST',
            url: '/ajax/',
            data: {ajax: true, imgID: imgID, method: 'setmainphotoe'}
        }).done(function(data){
            var data = $.parseJSON(data);
            if(data.code === 1) alert('Главная фотография изменена');
        });
        return false;
    });
    
    
    
    
    
    
    
    
    
    // для админки напишем здесь
    $( "#switchon, #switchoff" ).datepicker();
});