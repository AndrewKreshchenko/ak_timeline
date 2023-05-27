import { WidgetI } from './types';
import './utils/base';
export declare class Widget implements WidgetI {
    block: HTMLElement | HTMLTemplateElement;
    initOrder: number;
    _name: string;
    options?: any;
    static logStyle: string[];
    constructor(block: HTMLElement | null, initOrder: number, options?: any);
    protected setName: (name: string) => void;
    protected getName: () => string;
    info(): void;
    logException(message: string): void;
}
export declare class WidgetCollapsible extends Widget implements WidgetI {
    constructor(block: HTMLTemplateElement, initOrder: number, options?: any);
    info(): void;
    init(container: HTMLElement, points: NodeListOf<HTMLElement>, tplElems: string): void;
}
export declare class WidgetFormFilter extends Widget implements WidgetI {
    _error: boolean;
    constructor(block: HTMLFormElement, initOrder: number, options?: any);
    setError(): void;
    getError(): boolean;
    info(): void;
    formSubmitHandler(e: Event): void;
    init(): void;
}
export declare class WidgetScrollspy extends Widget implements WidgetI {
    constructor(block: HTMLTemplateElement, initOrder: number, options?: any);
    info(): void;
}
