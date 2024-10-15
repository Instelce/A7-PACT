/**
 * Create a template element with the given content
 * @param {string} content
 * @return HTMLTemplateElement
 */
export function createTemplate(content) {
    let template = document.createElement('template');
    template.innerHTML = content;
    return template;
}