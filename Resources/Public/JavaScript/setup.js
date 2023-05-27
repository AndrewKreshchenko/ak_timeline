(function (factory) {
  typeof define === 'function' && define.amd ? define(factory) :
  factory();
})((function () { 'use strict';

  // Handlers for DOM ready
  document.addEventListener('DOMContentLoaded', function (e) {
    // DOM element where the Timeline will be attached
    var container = document.querySelector('.tx-timeline');
    var points = container.querySelectorAll('.timeline-point');
    // NOTE For this time handle only vertical and horizontal timelines
    var timelineType = container.classList.contains('timeline-vertical') ? 'v' : container.classList.contains('timeline-horizontal') ? 'h' : 'n';
    var timeline;
    if (timelineType === 'v') {
      timeline = new tl.VerticalTimeline(timelineType, container, points);
    }

    //----------
    // Segments
    if (document.querySelector('[data-js="timeline-segment"]')) {
      // Make array of points from each segment
      document.querySelectorAll('[data-js="timeline-segment"]').joinTplNodes('.timeline-point');

      // Register widgets and set respective order to work them properly
      var tlWidgets = {};
      if (container.querySelector('[data-tl_widget]')) {
        // Go through each widget and populate 'tlWidgets' object with widgets
        // Thus sill be possible to set needed options for particular widgets.
        Array.from(container.querySelectorAll('[data-tl_widget]')).forEach(function (widgetElem) {
          switch (widgetElem.dataset.js) {
            case 'widget-accordion':
              tlWidgets.accordion = new tlw.WidgetCollapsible(widgetElem, 0, '.widget-accordion');
              break;
            case 'widget-form-filter':
              tlWidgets.formFilter = new tlw.WidgetFormFilter(widgetElem, 1, {
                timelineType: timelineType,
                container: container,
                pointsLen: points.length
              });
              break;
            case 'widget-scrollspy':
              tlWidgets.scrollspy = new tlw.WidgetScrollspy(widgetElem, 1);
              break;
          }
        });
      }

      // Use widgets methods (initialize)
      if (timelineType === 'v') {
        if (tlWidgets.accordion) {
          tlWidgets.accordion.init(container, points, '[data-js="timeline-segment"]');
        } else {
          timeline.spreadDerivedSegments(container.querySelectorAll('[data-js="timeline-segment"]'));
        }
      }
      tlWidgets.formFilter.init();
    }
  });

}));
//# sourceMappingURL=setup.js.map
