/**
 * Timeline setup script
 */

// Handlers for DOM ready
document.addEventListener('DOMContentLoaded', function(e) {
  // DOM element where the Timeline will be attached
  const container = document.querySelector('.tx-timeline');
  const points = container.querySelectorAll('.timeline-point');
  // NOTE For this time handle only vertical and horizontal timelines
  const timelineType = container.classList.contains('timeline-vertical') ? 'v' : (container.classList.contains('timeline-horizontal') ? 'h' : 'n');
  let timeline;

  if (timelineType === 'v') {
    timeline = new tl.VerticalTimeline(timelineType, container, points);
  }

  //----------
  // Segments
  if (document.querySelector('[data-js="timeline-segment"]')) {
    // Make array of points from each segment
    const tplPoints = document.querySelectorAll('[data-js="timeline-segment"]').joinTplNodes('.timeline-point');

    // Register widgets and set respective order to work them properly
    const tlWidgets = {};

    if (container.querySelector('[data-tl_widget]')) {
      // Go through each widget and populate 'tlWidgets' object with widgets
      // Thus sill be possible to set needed options for particular widgets.
      Array.from(container.querySelectorAll('[data-tl_widget]')).forEach(widgetElem => {
        switch (widgetElem.dataset.js) {
          case 'widget-accordion':
            tlWidgets.accordion = new tlw.WidgetCollapsible(widgetElem, 0, '.widget-accordion');
            break;
          case 'widget-form-filter':
            if (timelineType === 'v') {
              tlWidgets.formFilter = new tlw.WidgetFormFilter(widgetElem, 1, {
                timelineType: timelineType,
                container: container,
                pointsLen: points.length
              });
            }
            // else if (timelineType === 'h') {
            //   tlWidgets.formFilter = 'init after vis.Timeline';
            // }
            break;
          case 'widget-scrollspy':
            tlWidgets.scrollspy = new tlw.WidgetScrollspy(widgetElem, 1);
            break;
          default:
            break;
        }
      });

      // widgets.options = {
      //   ajaxURL: 
      // }
    }

    // Use widgets methods (initialize)
    if (timelineType === 'v') {
      if (tlWidgets.accordion) {
        tlWidgets.accordion.init(container, points, '[data-js="timeline-segment"]');
      } else {
        timeline.spreadDerivedSegments(container.querySelectorAll('[data-js="timeline-segment"]'));
      }

      if (tlWidgets.formFilter) {
        tlWidgets.formFilter.init();
      }
    }
  }

  if (timelineType === 'h' && container.dataset.url) {
    // DOM element where the Timeline will be attached
    const visBlock = document.querySelector('[data-js="timeline-horizontal"]');

    if (!visBlock) {
      console.warn('%cPoints are not exist for the Timeline yet, or error in Timeline found. Please check settings.', 'padding:15px;font-size:12px;font-weight:bold;');
      return;
    }

    const pointContainer = container.querySelector('.timeline');

    const handleClickVisItem = (e) => {
      e.preventDefault();

      const pointId = getClosest(e.target, '.vis-point').dataset.id;
      const templateElem = visBlock.nextElementSibling;
      const pointNode = templateElem.content.cloneNode(true);
      const pointBlock = pointNode.querySelector('.timeline[data-point_id="' + pointId + '"]');

      pointContainer.innerHTML = pointBlock.innerHTML;
    }

    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        try {
          var data = JSON.parse(xhr.responseText)

          console.log('Status ' + xhr.readyState);

          if (data.points && data.points.length) {
            // Prepare visual part
            const dataVisual = [];

            data.points.forEach((point, i) => {
              const date = dayjs(point.date.date, "YYYY-MM-DD");
              const dateFormat = date.format('DD MMM YYYY');

              dataVisual.push({
                id: `tl-${container.dataset.tl_id}-${point.id}`,
                type: 'point',
                start: dateFormat,
                title: point.title,
                dateTL: {
                  date: date.$d,
                  isBC: point.date.isBC
                },
                content: `<strong>${point.title}</strong><span>${dateFormat}</span>`,
              });
            });

            // Create a DataSet (allows two way data-binding)
            const items = new vis.DataSet(dataVisual);

            // Configuration for the Timeline
            const options = {
              dataAttributes: ['id'],
              height: 200,
              groupHeightMode: 'fixed'
            };

            // Create a Timeline
            timeline = new tl.HorizontalTimeline(timelineType, container, new vis.Timeline(visBlock, items, options));

            const tlwFormFilter = document.querySelector('[data-js="widget-form-filter"]');

            if (tlwFormFilter) {
              new tlw.WidgetFormFilter(tlwFormFilter, 1, {
                timelineType: 'h',
                container: container,
                pointsLen: dataVisual.length,
                // pass vis.js Timeline instance to use vis.Timeline API
                timelineVis: timeline.visTimeline,
                dataset: dataVisual,
              }).init();
            }
          }
        } catch(e) {
          console.error(e);
        }
      }
    };

    xhr.open('GET', container.dataset.url);
    xhr.send();
  }

});
