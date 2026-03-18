export function formatNumber(val) {
    if (!val) return '';

    const parts = val.toString().replace(/[^0-9.]/g, '').split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    return parts.length > 1 ? parts[0] + '.' + parts[1].substring(0, 8) : parts[0];
}

export function getRaw(val) {
    if (val === null || val === undefined) return '';

    return val.toString().replace(/,/g, '');
}

