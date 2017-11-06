'use strict';

var __runtimeTimes__ = -1;
var __activeList__ = [];
var getSettings = function(target, data) {
    return {
        type: "POST",
        url: target,
        data: JSON.stringify(data),
        dataType: "json",
        error: function(XHR, textStatus, errorThrown) {
            console.log(XHR);
            alert("XHR=" + XHR + "\ntextStatus=" + textStatus + "\nerrorThrown=" + errorThrown);
        },
        headers: {
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Method": "*"
        }
    };
};

var successSelectCallBack = function(resultData) {
    successCollectionCallBack(resultData);
    selectOne();
};

var successCollectionCallBack = function(resultData) {
    __activeList__.length = 0;
    __runtimeTimes__ = 0;
    $('.show-list').empty();
    var html = '<table class="table table-striped table-bordered">';
    html += '<th>待选店名</th>';
    html += '<th>该回权重</th>';
    html += '<th>前回日期</th>';
    html += '<th>选择回数</th>';

    for (var i = 0; i < resultData.length; i++) {
        html += '<tr>';
        html += '<td class="text-l">' + resultData[i].show_name + '</td>';
        html += '<td class="text-r">' + resultData[i].weight + '</td>';
        html += '<td class="text-c">' + resultData[i].pre_selected_date.substring(0, 10) + '</td>';
        html += '<td class="text-r">' + resultData[i].choose_times + '</td>';
        __activeList__.push({
            id: resultData[i].id,
            name: resultData[i].show_name,
            baseWeight: resultData[i].weight * (i + 1)
        });
        html += '</tr>';
    }
    html += '</table>';
    $('.show-list').html(html);
};

var sendBack = function(activedItem) {
    //id name baseWeight
    var setting = getSettings('api.php/set_actived', activedItem);
    setting.dataType = 'json';
    setting.success = successCollectionCallBack;
    $.ajax(setting);
};

var randomSort = function(arr, seed) {
    arr.sort(function(a, b) {
        return _getRandom(1, seed) - _getRandom(1, seed);
    });
};

var combineNumData = function(chars, length) {
    chars.sort(function(a, b) {
        return Math.random() - Math.random();
    });
    var result = 0;
    var resultLength = (length === undefined || length > chars.length) ? chars.length : length;
    for (var i = 0; i < resultLength; i++) {
        result += chars[i] * Math.pow(10, (resultLength - (i + 1)));
    }
    return result;
};

var _getRandom = function(from, to) {
    var c = from - to + 1;
    return Math.floor(Math.random() * c + to);
};

var choose = function(runtimeList) {
    __runtimeTimes__--;
    $('.times').empty();
    $('.times').html(__runtimeTimes__);
    $('.runtime').empty();
    randomSort(runtimeList, combineNumData([1, 3, 5, 7]));
    var rd = _getRandom(0, runtimeList.length - 1);
    $('.runtime').html('<span style="color: ' + getRandomColor() + '">' + runtimeList[rd].name + '</span>');
    if (__runtimeTimes__ > 0)
        setTimeout(function() {
            choose(runtimeList);
        }, parseInt(combineNumData([1, 3, 7]) * 1.37 / 2));
    else sendBack(runtimeList[rd]);
};

var selectOne = function() {
    //_getRandom(1, 9)
    __runtimeTimes__ = combineNumData([1, 2, 3, 4, 5, 6, 7, 8, 9], _getRandom(3, 7));
    var runtimeList = [];
    for (var i = 0; i < __activeList__.length; i++) {
        for (var j = 0; j < __activeList__[i].baseWeight; j++) {
            runtimeList.push(__activeList__[i]);
        }
    }
    var rdSeed = _getRandom(0, combineNumData([1, 3, 5, 7]));
    for (var i = 0; i < rdSeed; i++) randomSort(runtimeList, rdSeed);
    setTimeout(function() {
        choose(runtimeList);
    }, parseInt(combineNumData([1, 3, 7]) * 1.37 / 2));
};

var getRandomColor = function() {
    return '#' + ('00000' + (Math.random() * 0x1000000 << 0).toString(16)).slice(-6);
}

$(document).ready(function() {
    var postData = {};
    var setting = getSettings('api.php/get_items', postData);
    setting.dataType = 'json';
    setting.success = successSelectCallBack;
    $.ajax(setting);
});