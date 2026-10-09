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
    if (definitions[language]) {
        try { code.innerHTML = hljs.highlight(content, { language, ignoreIllegals: true }).value; }
        catch { code.textContent = content; }
    } else code.textContent = content;
}
