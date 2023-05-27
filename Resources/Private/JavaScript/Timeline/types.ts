/**
 * Timeline types
 */

/**
 * Timeline view types:
 * 'v' - vertical
 * 'h' - horizontal
 * 'b' - bars-styled
 * 'p' - pie
 * 'n' - none of above types (generate Exception)
 */
export type TLVariantType = 'v'|'h'|'b'|'p'|'n';

export type TLDateType = {
  date: Date,
  isBC: boolean
}

export type TplPointsType = {
  index: number,
  dateTL: TLDateType,
  dateStr: string,
}

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
  _name: string,
  options?: any
}
