export type TLVariantType = 'v' | 'h' | 'b' | 'p' | 'n';
export type TLDateType = {
    date: Date;
    isBC: boolean;
};
export type TplPointsType = {
    index: number;
    dateTL: TLDateType;
    dateStr: string;
};
export type RouteType = {
    dataId: number;
    ajaxURL: string;
};
export type RangeTuple = [TLDateType, TLDateType];
export interface TimelineI {
    type: TLVariantType;
    container: HTMLElement;
    dateStart?: TLDateType;
    dateEnd?: TLDateType;
    _pointsLen?: number;
}
export interface WidgetI {
    block: HTMLElement | HTMLTemplateElement | HTMLFormElement;
    initOrder: number;
    _name: string;
    options?: any;
}
