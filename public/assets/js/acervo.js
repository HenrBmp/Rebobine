(() => {
  const search = document.querySelector('#catalog-search');
  const chips = [...document.querySelectorAll('[data-filter]')];
  const cards = [...document.querySelectorAll('[data-kind]')];
  const count = document.querySelector('#catalog-count');
  let active = 'todos';
  const update = () => {
    const term = (search?.value || '').trim().toLocaleLowerCase('pt-BR');
    let visible = 0;
    cards.forEach(card => {
      const matchesKind = active === 'todos' || card.dataset.kind === active;
      const matchesText = !term || card.dataset.search.includes(term);
      const show = matchesKind && matchesText;
      card.hidden = !show;
      if (show) visible++;
    });
    if (count) count.textContent = `${visible} ${visible === 1 ? 'TÍTULO' : 'TÍTULOS'}`;
  };
  search?.addEventListener('input', update);
  chips.forEach(chip => chip.addEventListener('click', () => {
    active = chip.dataset.filter;
    chips.forEach(item => item.setAttribute('aria-pressed', String(item === chip)));
    update();
  }));
  update();

  // O slogan alterna entre adjetivos a cada clique.
  const sloganButton = document.querySelector('#slogan-switch');
  const sloganWord = document.querySelector('#slogan-word');
  const sloganWords = ['GENTIL', 'SUBLIME', 'INCRÍVEL', 'LIVRE', 'NOSTÁLGICO'];
  let sloganIndex = 0;
  sloganButton?.addEventListener('click', () => {
    sloganIndex = (sloganIndex + 1) % sloganWords.length;
    if (sloganWord) sloganWord.textContent = sloganWords[sloganIndex];
  });
})();
