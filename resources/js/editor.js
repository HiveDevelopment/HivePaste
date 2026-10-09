import { EditorState, Compartment } from '@codemirror/state';
import { EditorView, keymap, lineNumbers, highlightActiveLineGutter, highlightSpecialChars, drawSelection, highlightActiveLine } from '@codemirror/view';
import { defaultKeymap, history, historyKeymap, indentWithTab } from '@codemirror/commands';
import { indentOnInput, bracketMatching, foldGutter, syntaxHighlighting } from '@codemirror/language';
import { HighlightStyle } from '@codemirror/language';
import { tags } from '@lezer/highlight';
import { autocompletion, closeBrackets, closeBracketsKeymap } from '@codemirror/autocomplete';
import { searchKeymap, highlightSelectionMatches } from '@codemirror/search';
import { languages } from '@codemirror/language-data';

const input = document.getElementById('content');
const mount = document.getElementById('codemirror-editor');
const fallback = document.getElementById('textarea-editor');
const languageInput = document.getElementById('language');
if (input && mount && fallback && languageInput) {
    const language = new Compartment();
    const sync = EditorView.updateListener.of(update => {
        if (update.docChanged) {
            input.value = update.state.doc.toString();
            document.dispatchEvent(new Event('hivepaste:editor-change'));
        }
    });
    // Native HivePaste dark palette; avoid CodeMirror's grey-blue oneDark defaults.
    const theme = EditorView.theme({
        '&': { height: 'clamp(320px, 48vh, 520px)', backgroundColor: '#0c0c0d', color: '#e4e4e7', fontSize: '14px' },
        '&.cm-focused': { outline: 'none' },
        '.cm-scroller': { height: '100%', overflow: 'auto', fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Consolas, monospace', lineHeight: '1.65' },
        '.cm-content': { minHeight: '100%', padding: '12px 0', caretColor: '#f58218' },
        '.cm-line': { padding: '0 14px' },
        '.cm-gutters': { backgroundColor: '#101011', borderRight: '1px solid #292523', color: '#71717a' },
        '.cm-gutterElement': { padding: '0 10px 0 12px' },
        '.cm-activeLine': { backgroundColor: '#181513' },
        '.cm-activeLineGutter': { backgroundColor: '#201a15', color: '#f5a34b' },
        '.cm-cursor, .cm-dropCursor': { borderLeftColor: '#f58218' },
        '.cm-selectionBackground, &.cm-focused .cm-selectionBackground': { backgroundColor: '#69401f80' },
        '.cm-selectionMatch': { backgroundColor: '#48301d' },
        '.cm-matchingBracket': { backgroundColor: '#3c2a19', outline: '1px solid #f58218' },
        '.cm-foldPlaceholder': { backgroundColor: '#211b16', borderColor: '#35302b', color: '#e4e4e7' },
        '.cm-tooltip': { backgroundColor: '#151515', border: '1px solid #35302b', color: '#e4e4e7' },
        '.cm-tooltip-autocomplete ul li[aria-selected]': { backgroundColor: '#392517', color: '#fff' },
        '.cm-panels': { backgroundColor: '#151515', color: '#e4e4e7' },
        '.cm-searchMatch': { backgroundColor: '#70421c70' }
    }, { dark: true });
    const hiveHighlight = syntaxHighlighting(HighlightStyle.define([
        { tag: tags.keyword, color: '#fb923c' },
        { tag: [tags.controlKeyword, tags.moduleKeyword], color: '#fdba74', fontWeight: '600' },
        { tag: [tags.string, tags.special(tags.string)], color: '#a3e635' },
        { tag: [tags.number, tags.bool, tags.null], color: '#fbbf24' },
        { tag: [tags.comment, tags.lineComment, tags.blockComment], color: '#85858f', fontStyle: 'italic' },
        { tag: [tags.function(tags.variableName), tags.function(tags.propertyName)], color: '#7dd3fc' },
        { tag: [tags.typeName, tags.className, tags.namespace], color: '#fcd34d' },
        { tag: [tags.tagName, tags.attributeName], color: '#fb923c' },
        { tag: [tags.propertyName, tags.definition(tags.propertyName)], color: '#93c5fd' },
        { tag: [tags.operator, tags.punctuation], color: '#c4c4cc' },
        { tag: tags.invalid, color: '#f87171', textDecoration: 'underline' }
    ]));
    const editor = new EditorView({
        state: EditorState.create({
            doc: input.value,
            extensions: [
                lineNumbers(), highlightActiveLineGutter(), highlightSpecialChars(), history(),
                foldGutter(), drawSelection(), indentOnInput(), bracketMatching(), closeBrackets(),
                autocompletion(), highlightSelectionMatches(), highlightActiveLine(),
                keymap.of([
                    { key: 'Mod-Enter', run: () => { input.form?.requestSubmit(); return true; } },
                    indentWithTab, ...closeBracketsKeymap, ...defaultKeymap, ...searchKeymap, ...historyKeymap
                ]),
                language.of([]), theme, hiveHighlight, sync,
                []
            ]
        }), parent: mount
    });
    // Values must match the language options in resources/views/editor.blade.php.
    // CodeMirror language-data uses different names for some formats (e.g. Shell,
    // Dockerfile), so do not rely on the select value being an exact display name.
    const languageNames = {
        text: null,
        log: null,
        json: 'JSON',
        javascript: 'JavaScript',
        typescript: 'TypeScript',
        php: 'PHP',
        python: 'Python',
        java: 'Java',
        yaml: 'YAML',
        bash: 'Shell',
        sql: 'SQL',
        html: 'HTML',
        css: 'CSS',
        go: 'Go',
        rust: 'Rust',
        xml: 'XML',
        dockerfile: 'Dockerfile'
    };
    let languageChange = 0;

    async function setLanguage(value) {
        const change = ++languageChange;
        const name = languageNames[value.toLowerCase()];
        const description = name ? languages.find(item => item.name.toLowerCase() === name.toLowerCase() ||
            item.alias?.some(alias => alias.toLowerCase() === name.toLowerCase())) : null;

        try {
            const support = description ? await description.load() : [];
            // If someone changes languages while a module is loading, don't let
            // the older result overwrite the latest selection.
            if (change !== languageChange) return;
            editor.dispatch({ effects: language.reconfigure(support) });
        } catch (error) {
            if (change !== languageChange) return;
            editor.dispatch({ effects: language.reconfigure([]) });
            console.warn(`Syntax highlighting unavailable for ${value}`, error);
        }
    }
    languageInput.addEventListener('change', () => { void setLanguage(languageInput.value); });
    void setLanguage(languageInput.value);
    window.hivepasteEditor = {
        getValue: () => editor.state.doc.toString(),
        setValue: value => editor.dispatch({ changes: { from: 0, to: editor.state.doc.length, insert: value } })
    };
    fallback.classList.add('hidden');
    mount.classList.remove('hidden');
    input.form?.addEventListener('submit', () => { input.value = editor.state.doc.toString(); });
}
