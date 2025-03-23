const translations = {
// later
};

let isEnglish = false;

function translateText(text) {
    if (!isEnglish) return text; 
    let words = text.split(" ");
    let translated = words.map(word => translations[word] || word).join(" ");
    return translated;
}

function walkTextNodes(node) {
    if (node.nodeType === 3) { 
        const originalText = node.data.trim();
        if (originalText) {
            if (!node.parentElement.dataset.originalText) {
                node.parentElement.dataset.originalText = originalText;
            }
            node.data = translateText(originalText);
        }
    } else if (node.nodeType === 1 && node.tagName !== 'SCRIPT' && node.tagName !== 'STYLE') { 
        for (let child of node.childNodes) {
            walkTextNodes(child);
        }
    }
}

function toggleLanguage() {
    const button = document.querySelector('.lang-button');
    const html = document.documentElement;

    isEnglish = !isEnglish;

    if (isEnglish) {
        button.textContent = 'العربية';
        html.setAttribute('lang', 'en');
        html.setAttribute('dir', 'ltr');
        document.title = translateText(document.title);
        walkTextNodes(document.body); 
    } else {
        button.textContent = 'English';
        html.setAttribute('lang', 'ar');
        html.setAttribute('dir', 'rtl');
        document.title = "نظام جدولة وحجز المواعيد"; 
        const allElements = document.body.getElementsByTagName('*');
        for (let element of allElements) {
            if (element.dataset.originalText) {
                element.textContent = element.dataset.originalText;
            }
        }
    }
}