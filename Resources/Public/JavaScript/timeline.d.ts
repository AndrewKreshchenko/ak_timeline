import { TLVariantType, TLDateType, TimelineI } from './types';
import './utils/base';
export declare class Timeline implements TimelineI {
    readonly type: TLVariantType;
    container: HTMLElement;
    dateStart?: TLDateType;
    dateEnd?: TLDateType;
    _pointsLen?: number;
    constructor(type: TLVariantType, container: HTMLElement, dateStart?: TLDateType, dateEnd?: TLDateType);
    protected setPointsLength: (len: number) => void;
    protected getPointsLength: () => number;
    protected setRange: (dateStart: TLDateType, dateEnd: TLDateType) => void;
    logRange(): string;
    log(): void;
    logException(index: number): void;
}
export declare class VerticalTimeline extends Timeline {
    points: NodeListOf<HTMLElement>;
    constructor(type: TLVariantType, container: HTMLElement, points: NodeListOf<HTMLElement>);
    spreadDerivedSegments(tplElems: any): void;
}
export declare class HorizontalTimeline extends Timeline {
    visTimeline: ObjectConstructor;
    constructor(type: TLVariantType, container: HTMLElement, visTimeline: ObjectConstructor);
    init(): void;
}
