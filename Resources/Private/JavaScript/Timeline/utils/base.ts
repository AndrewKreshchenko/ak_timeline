interface Object {
  joinTplNodes(selector: string): Element[]
}

if (typeof Object.prototype.joinTplNodes !== 'function') {
  Object.defineProperty(Object.prototype, 'joinTplNodes', {
    value: function(selector: string) {
      const nodes: Element[] = Array.from(this).map((elem: HTMLTemplateElement) => {
        const tplNode = elem.content.cloneNode(true);
        const tplElements = (<Element>tplNode).querySelectorAll(selector);
    
        return Array.prototype.slice.call(tplElements);
      });
  
      return nodes.flat();
    }
  });
}
