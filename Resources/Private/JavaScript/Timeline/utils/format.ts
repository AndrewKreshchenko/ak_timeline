const numberRegexp = /^[0-9]+$/;

export function getExtDateString(date: Date, isBC: Boolean):String {
  return (isBC ? '-' : '') + date.toDateString();
}

export function getBCDate(date: string, isBC: boolean): Date {
  if (isBC) {
    const dateSlices = [Number(date.slice(0, 4)), date.slice(4) + 'T00:00:00'];
    const diff = new Date((1970 - (dateSlices[0] as number)) + (dateSlices[1] as string));

    return new Date(diff.valueOf() - 62167226524000);
  } else {
    return new Date(date);
  }
}
