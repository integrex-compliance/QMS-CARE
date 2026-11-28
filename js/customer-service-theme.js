(function (root) {
    'use strict';
    var palettes = {
        qms: ['#1558b0', '#eff6ff', '#ffffff'],
        ohsms: ['#c2410c', '#fff7ed', '#ffffff'],
        ems: ['#15803d', '#f0fdf4', '#ffffff'],
        pollution: ['#0f766e', '#f0fdfa', '#ffffff'],
        zed: ['#475569', '#f8fafc', '#ffffff'],
        gold: ['#d4af37', '#fffbeb', '#302500'],
        silver: ['#cbd5e1', '#f8fafc', '#1e293b'],
        bronze: ['#b87942', '#fff7ed', '#241407'],
        food: ['#4d7c0f', '#f7fee7', '#ffffff'],
        security: ['#6d28d9', '#f5f3ff', '#ffffff'],
        energy: ['#a16207', '#fefce8', '#ffffff'],
        automotive: ['#334155', '#f1f5f9', '#ffffff'],
        isi: ['#4338ca', '#eef2ff', '#ffffff'],
        ce: ['#0369a1', '#f0f9ff', '#ffffff'],
        trade: ['#9d174d', '#fdf2f8', '#ffffff']
    };
    function theme(name, stage) {
        name = String(name || '').toLowerCase();
        stage = String(stage || '').toLowerCase();
        var key;
        if (/\bzed\b|zero defect/.test(name)) {
            key = 'zed';
            var grade = stage || name;
            if (/\bgold\b/.test(grade)) key = 'gold';
            else if (/\bsilver\b/.test(grade)) key = 'silver';
            else if (/\bbron[sz]e\b/.test(grade)) key = 'bronze';
        } else if (/45001|18001|\bohs(ms|as)?\b|occupational/.test(name)) key = 'ohsms';
        else if (/14001|\bems\b|environment/.test(name)) key = 'ems';
        else if (/\b(mpcb|cpcb|spcb)\b|pollution/.test(name)) key = 'pollution';
        else if (/9001|\bqms\b|quality management/.test(name)) key = 'qms';
        else if (/22000|\b(fsms|fssai|fssi|haccp)\b|food/.test(name)) key = 'food';
        else if (/27001|\bisms\b|information security/.test(name)) key = 'security';
        else if (/50001|energy/.test(name)) key = 'energy';
        else if (/16949|\biatf\b|automotive/.test(name)) key = 'automotive';
        else if (/\b(isi|bis)\b/.test(name)) key = 'isi';
        else if (/\b(ce|nsic)\b/.test(name)) key = 'ce';
        else if (/trade|copyright/.test(name)) key = 'trade';
        else {
            var hash = 0;
            for (var i = 0; i < name.length; i++) hash = ((hash * 31) + name.charCodeAt(i)) >>> 0;
            key = ['qms', 'pollution', 'security', 'ce', 'trade'][hash % 5];
        }
        var colors = palettes[key];
        return {key: key, color: colors[0], soft: colors[1], ink: colors[2]};
    }
    if (typeof module === 'object' && module.exports) module.exports = theme;
    else root.customerServiceTheme = theme;
})(typeof window !== 'undefined' ? window : globalThis);
