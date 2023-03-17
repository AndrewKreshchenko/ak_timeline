const numberRegexp = /^[0-9]+$/;
export default function (date, isBC) {
    return (isBC ? '-' : '') + date.toDateString();
}

//# sourceMappingURL=format.js.map
