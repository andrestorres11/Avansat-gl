(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global = global || self, (global.FullCalendarLocales = global.FullCalendarLocales || {}, global.FullCalendarLocales.es = factory()));
}(this, function () { 'use strict';

    var es = {
        code: "es",
        week: {
            dow: 1,
            doy: 4 // The week that contains Jan 4th is the first week of the year.
        },
        buttonText: {
            prev: "Ant",
            next: "Sig",
            today: "Hoy",
            month: "Mes",
            week: "Semana",
            day: "D\u00eda",
            list: "Agenda"
        },
        weekLabel: "Sm",
        allDayHtml: "Todo<br/>el d\u00eda",
        eventLimitText: "m\u00e1s",
        noEventsMessage: "No hay eventos para mostrar"
    };

    return es;

}));
