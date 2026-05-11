export function fmtIdr(amount, compact = false) {
    const n = Number(amount) || 0;
    if (compact) {
        const abs = Math.abs(n);
        if (abs >= 1_000_000_000) return 'Rp ' + (n / 1_000_000_000).toFixed(1) + 'B';
        if (abs >= 1_000_000) return 'Rp ' + (n / 1_000_000).toFixed(1) + 'M';
        if (abs >= 1_000) return 'Rp ' + (n / 1_000).toFixed(1) + 'k';
    }
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
}

export function fmtNumber(amount) {
    return new Intl.NumberFormat('id-ID').format(Number(amount) || 0);
}

export function fmtDate(d) {
    if (!d) return '—';
    try {
        return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    } catch {
        return d;
    }
}

export function fmtDateTime(d) {
    if (!d) return '—';
    return new Date(d).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

export function fmtPercent(value) {
    return new Intl.NumberFormat('id-ID', { style: 'percent', maximumFractionDigits: 1 }).format(Number(value) || 0);
}
