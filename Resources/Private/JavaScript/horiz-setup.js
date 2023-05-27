// TODO utilise in TS module
function getClosest(elem, selector) {
  if (!elem.matches && !elem.mozMatchesSelector) {
    return null;
  }

  while (elem !== document.body) {
    elem = elem.parentElement;
    if (elem.matches) {
      if (elem.matches(selector)) {
        return elem;
      }
    } else if (elem.mozMatchesSelector) {
      if (elem.mozMatchesSelector(selector)) {
        return elem;
      }
    }
  }
}

document.addEventListener('DOMContentLoaded', function(e) {
  // DOM element where the Timeline will be attached
  const visBlock = document.querySelector('[data-js="timeline-horizontal"]');

  if (!visBlock) {
    console.warn('%cPoints are not exist for the Timeline yet, or error in Timeline found. Please check settings.', 'padding:15px;font-size:12px;font-weight:bold;');
    return;
  }

  // const dataBlock = visBlock.parentNode.querySelector('[data-js="timeline-data"]');
  const container = document.querySelector('.tx-timeline[data-tl_id]');
  const pointContainer = container.querySelector('.timeline');
  const dataId = container.dataset.tl_id;
  const ajaxURL = container.dataset.url;

  const handleClickVisItem = (e) => {
    e.preventDefault();

    const pointId = getClosest(e.target, '.vis-point').dataset.id;
    const templateElem = visBlock.nextElementSibling;
    const pointNode = templateElem.content.cloneNode(true);
    const pointBlock = pointNode.querySelector('.timeline[data-point_id="' + pointId + '"]');

    pointContainer.innerHTML = pointBlock.innerHTML;
  }

  // GET data by action

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
            const dateFormat = dayjs(point.date.date, "YYYY-MM-DD").format('DD MMM YYYY');

            dataVisual.push({
              id: `tl-${dataId}-${point.id}`,
              type: 'point',
              start: dateFormat,
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
          const timeline = new vis.Timeline(visBlock, items, options);

          // Open content block by clicked point
          container.querySelectorAll('.vis-item-content').forEach((point) => {
            point.addEventListener('click', handleClickVisItem);
          });
        }
      } catch(e) {
        console.error(e);
      }
    }
  };

  // Test using middleware:
  // xhr.open('GET', 'http://localhost:8000/index.php?tlinfo=true&pid=' + dataId);

  xhr.open('GET', ajaxURL);
  xhr.send();

});
