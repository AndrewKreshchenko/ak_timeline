// Utility functions
Object.prototype.joinTplNodes = function(selector) {
  const nodes = Array.from(this).map(elem => {
    const tplNode = elem.content.cloneNode(true);
    const tplElements = tplNode.querySelectorAll(selector);

    return Array.prototype.slice.call(tplElements);
  });

  return Array.prototype.concat.call(...nodes);
}

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

// Handlers for DOM ready
document.addEventListener('DOMContentLoaded', function(e) {
  // DOM element where the Timeline will be attached
  // const dataBlock = visBlock.parentNode.querySelector('[data-js="timeline-data"]');
  // const container = document.querySelector('.tx-timeline[data-tl_id]');
  const container = document.querySelector('.tx-timeline');
  const points = container.querySelectorAll('.timeline');
  // const pointContainer = container.querySelector('.timeline');
  // const dataId = container.dataset.tl_id;
  // const ajaxURL = container.dataset.url;

  const handleClickVisItem = (e) => {
    e.preventDefault();

    const pointId = getClosest(e.target, '.vis-point').dataset.id;
    const templateElem = visBlock.nextElementSibling;
    const pointNode = templateElem.content.cloneNode(true);
    const pointBlock = pointNode.querySelector('.timeline[data-point_id="' + pointId + '"]');

    pointContainer.innerHTML = pointBlock.innerHTML;
  }

  //----------
  // Segments
  if (document.querySelector('[data-js="timeline-segment"]')) {
    // Make array of points from each segment
    const tplPoints = document.querySelectorAll('[data-js="timeline-segment"]').joinTplNodes('.timeline');

    // Spread points of each segments
    const pointsLen = tplPoints.length;

    const timePoints = Array.from(points).map(point => {
      const dateTime = point.querySelector('time').getAttribute('datetime');
      return {
        date: new Date(dateTime),
        dateStr: dateTime,
        isBC: Boolean(point.dataset.not_ad)
      }
    });

    const timeTplPoints = tplPoints.map((point, i) => ({
      index: i,
      date: new Date(point.dataset.date),
      dateStr: point.dataset.date,
      isBC: Boolean(point.dataset.not_ad)
    }));

    timeTplPoints.sort((prev, next) => {
      return next.isBC ? next.date - prev.date : prev.date - next.date;
    });

    // For each shown point of parent timeline check through the points of derived timelines 
    let pointDateStr, pointTime, iterate;

    for (let i = 0, m = 0; i < pointsLen; i++) {
      pointDateStr = points[i].querySelector('time').getAttribute('datetime');
      pointTime = new Date(pointDateStr);
      iterate = true;

      // Iterate through child points since their dates prevail over a point is being tested 
      while (iterate) {
        if (timePoints[i].isBC) {
          if (timeTplPoints[m].isBC && timeTplPoints[m].date >= pointTime) {
            points[i].before(tplPoints[m]);
            m++;
          } else {
            iterate = false;
          }
        } else {
          if (!timeTplPoints[m]) {
            // Tpl points list is drain
            return;
          }

          if (timeTplPoints[m].isBC) {
            points[i].before(tplPoints[m]);
            m++;
          } else if (timeTplPoints[m].date <= pointTime) {
            points[i].before(tplPoints[m]);
            m++;
          } else {
            iterate = false;
          }
        }
      }
    }
  }

});
