import hljs from 'highlight.js/lib/core';
import javascript from 'highlight.js/lib/languages/javascript';
import typescript from 'highlight.js/lib/languages/typescript';
import php from 'highlight.js/lib/languages/php';
import python from 'highlight.js/lib/languages/python';
import java from 'highlight.js/lib/languages/java';
import json from 'highlight.js/lib/languages/json';
import yaml from 'highlight.js/lib/languages/yaml';
import bash from 'highlight.js/lib/languages/bash';
import sql from 'highlight.js/lib/languages/sql';
import xml from 'highlight.js/lib/languages/xml';
import css from 'highlight.js/lib/languages/css';
import go from 'highlight.js/lib/languages/go';
import rust from 'highlight.js/lib/languages/rust';
import dockerfile from 'highlight.js/lib/languages/dockerfile';
const definitions = { javascript, typescript, php, python, java, json, yaml, bash, sql, html: xml, xml, css, go, rust, dockerfile };
for (const [name, grammar] of Object.entries(definitions)) hljs.registerLanguage(name, grammar);
const data = document.getElementById('paste-data');
const code = document.getElementById('highlighted');

if (data && code) {
    const { content, language } = JSON.parse(data.textContent);

    if (language === 'log') {
        // Styles are deliberately explicit: no dependency on Tailwind class scanning.
        const style = document.createElement('style');
        style.textContent = `
            #highlighted .hp-log-time { color: #85858e; }
            #highlighted .hp-log-info { color: #4ade80; font-weight: 600; }
            #highlighted .hp-log-warn { color: #fbbf24; font-weight: 600; }
            #highlighted .hp-log-error { color: #fb7185; font-weight: 600; }
            #highlighted .hp-log-debug { color: #60a5fa; font-weight: 600; }
            #highlighted .hp-log-trace { color: #a1a1aa; }
            #highlighted .hp-log-exception { color: #fdba74; }
        `;
        document.head.appendChild(style);
        const colors = {
            INFO: 'hp-log-info', INFORMATION: 'hp-log-info',
            WARN: 'hp-log-warn', WARNING: 'hp-log-warn',
            ERROR: 'hp-log-error', SEVERE: 'hp-log-error', FATAL: 'hp-log-error',
            DEBUG: 'hp-log-debug', TRACE: 'hp-log-trace',
        };
        const append = (text, className) => {
            if (!text) return;
            if (!className) { code.appendChild(document.createTextNode(text)); return; }
            const span = document.createElement('span');
            span.className = className;
            span.textContent = text;
            code.appendChild(span);
        };
        code.replaceChildren();
        content.split('\n').forEach((rawLine, index) => {
            if (index) append('\n');
            // Ignore terminal ANSI colour sequences for display; original data stays intact.
            let line = rawLine.replace(/\x1b\[[0-?]*[ -/]*[@-~]/g, '');
            const panelTime = line.match(/^\d{1,2}:\d{2}:\d{2}(?:[.,]\d+)?\s+/);
            if (panelTime) { append(panelTime[0], 'hp-log-time'); line = line.slice(panelTime[0].length); }

            // Paper/Spigot: [16:05:23 INFO]: message
            const minecraft = line.match(/^(\[\d{1,2}:\d{2}:\d{2}\s+)(INFO|INFORMATION|WARN|WARNING|ERROR|SEVERE|FATAL|DEBUG|TRACE)(\]\s*:?\s*)/i);
            if (minecraft) {
                append(minecraft[1], 'hp-log-time');
                append(minecraft[2], colors[minecraft[2].toUpperCase()]);
                append(minecraft[3], 'hp-log-time');
                line = line.slice(minecraft[0].length);
            } else {
                // ISO 8601 or space-delimited timestamp, optionally followed by a logger name.
                const iso = line.match(/^(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:[.,]\d+)?(?:Z|[+-]\d{2}:?\d{2})?\s+)/);
                if (iso) { append(iso[0], 'hp-log-time'); line = line.slice(iso[0].length); }
                const severity = line.match(/^(.*?\b)(INFO|INFORMATION|WARN|WARNING|ERROR|SEVERE|FATAL|DEBUG|TRACE)(?=\s|\]|:|$)(\s*[:\]\-]?\s*)/i);
                // Only treat a severity as a header when it occurs close to the start.
                if (severity && severity[1].length <= 48) {
                    append(severity[1]);
                    append(severity[2], colors[severity[2].toUpperCase()]);
                    append(severity[3]);
                    line = line.slice(severity[0].length);
                }
            }
            append(line, /(?:^\s*at\s+\S+|Caused by:|Suppressed:|\b(?:Exception|Error):)/.test(line) ? 'hp-log-exception' : undefined);
        });
    } else if (definitions[language]) {
        try { code.innerHTML = hljs.highlight(content, { language, ignoreIllegals: true }).value; }
        catch { code.textContent = content; }
    } else {
        code.textContent = content;
    }
}
