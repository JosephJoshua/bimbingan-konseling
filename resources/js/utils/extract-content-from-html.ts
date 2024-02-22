const extractContentFromHtml = (html: string) => {
  const el = document.createElement('span');
  el.innerHTML = html;

  const children = el.querySelectorAll<HTMLElement>('*');
  for (const child of children) {
    if (child.textContent) {
      child.textContent += ' ';
    } else {
      child.innerText += ' ';
    }
  }

  return (el.textContent || el.innerText).replace(/ +/g, ' ');
};

export default extractContentFromHtml;
