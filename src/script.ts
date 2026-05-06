
const styles = [
    { id: 1, name: 'Styl1', href: 'style-1.css' },
    { id: 2, name: 'Styl2', href: 'style-2.css' },
    { id: 3, name: 'Styl3', href: 'style-3.css' }
];


const container = document.querySelector('.tscsssstyle') as HTMLDivElement;


let styleLink = document.querySelector('link[rel="stylesheet"]') as HTMLLinkElement;
if (!styleLink) {
    styleLink = document.createElement('link');
    styleLink.rel = 'stylesheet';
    document.head.appendChild(styleLink);
}


styles.forEach(style => {
    const button = document.createElement('button');
    button.textContent = `przycisk ${style.id}`;
    button.id = `przycisk-${style.id}`;


    button.addEventListener('click', () => {
        styleLink.href = style.href;
    });

    container.appendChild(button);
});
