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
  const points = document.querySelectorAll('.tx-timeline .timeline');
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
    // const pointNode = segment.content.cloneNode(true);
    // const tplPoints = pointNode.querySelectorAll('.timeline');
    const pointsLen = tplPoints.length;
    let html;

    // const segRangeStart = new Date(segment.dataset.rangeStart);
    // const segRangeEnd = new Date(segment.dataset.rangeEnd);

    for (let i = 0; i < pointsLen; i++) {
      const time = new Date(points[i].querySelector('time').getAttribute('datetime'));
      html = '';

      console.log(i, time.getDate());

      for (let j = 0; j < tplPoints.length; j++) {
        const tplTime = new Date(tplPoints[j].dataset.date);
        console.log(tplPoints[j].dataset.date);
    
        if (tplTime >= time && i < pointsLen - 1) {
          console.log(i + ' append after, there');
          points[i + 1].before(tplPoints[j]);
        } else if (tplTime < time) {
          console.log(i + ' append before, there');
          points[i].previousElementSibling.after(tplPoints[j]);
        }
      }
    }
  }

});
