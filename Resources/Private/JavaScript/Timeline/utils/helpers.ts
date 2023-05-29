/**
 * Retrieves DOM HTML element from <template>
 * 
 * @param element HTMLTemplateElement
 * @param tplSelector selector to query by
 * @returns HTMLElement
 */
export function getTemplateElem(element: HTMLTemplateElement, tplSelector?: string) {
  const itemNode = element.content.cloneNode(true);

  if (!tplSelector) {
    return (itemNode as HTMLElement).firstElementChild;
  }

  return (itemNode as HTMLElement).querySelectorAll(tplSelector);
}

/**
 * Methods is traversing up through its ancestors in the DOM tree untill finds an element by selector, as in jQuery lib.
 * Returns null if nothing found
 * 
 * @param elem HTMLElement
 * @param selector - selector which parent (searched block) may have
 * @returns HTMLElement|null
 */
export function getClosest(elem: HTMLElement, selector: string): HTMLElement|null {
  if (!elem.matches) {
    return null;
  }

  if (elem.matches(selector)) {
    return elem;
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
