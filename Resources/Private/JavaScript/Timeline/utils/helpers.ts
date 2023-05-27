export function getTemplateElem(element: HTMLTemplateElement, tplSelector?: string) {
  const itemNode = element.content.cloneNode(true);

  if (!tplSelector) {
    return (itemNode as HTMLElement).firstElementChild;
  }

  return (itemNode as HTMLElement).querySelectorAll(tplSelector);
}

export function getClosest(elem: HTMLElement, selector: string) {
  if (!elem.matches) {
    return null;
  }

  while (elem !== document.body) {
    elem = elem.parentElement;
    if (elem.matches) {
      if (elem.matches(selector)) {
        return elem;
      }
    }
  }
}

// export function findFirstChildByClass(element: HTMLElement, className: string): HTMLElement {
//   let foundElement = null, found;

//   const search = (element: HTMLElement, className: string, found: Boolean) => {
//     for (const i = 0; i < element.childNodes.length && !found; i++) {
//       const el = element.childNodes[i];
//       const classes = el.className != undefined? el.className.split(" ") : [];
//       for (let j = 0, jl = classes.length; j < jl; j++) {
//         if (classes[j] == className) {
//           found = true;
//           foundElement = element.childNodes[i];
//           break;
//         }
//       }
//       if (found) {
//         break;
//       }
          
//       search((element as HTMLElement).childNodes[i], className, found);
//     }
//   }
//   recurse(element, className, false);
//   return foundElement;
// }
