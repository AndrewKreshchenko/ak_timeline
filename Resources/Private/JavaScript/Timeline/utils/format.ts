const numberRegexp = /^[0-9]+$/;

export default function (date: Date, isBC: Boolean):String {
  console.log(numberRegexp.test(date.toDateString()));
  return (isBC ? '-' : '') + date.toDateString();
}
