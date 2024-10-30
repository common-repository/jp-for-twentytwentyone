let jpForTwentyTwentyOneToc = () => {
  /**
   * スムーズスクロール関数
   */
  let smoothScroll = (target, offset) => {
    const targetRect = target.getBoundingClientRect();
    const targetY = targetRect.top + window.pageYOffset - offset;
    window.scrollTo({left: 0, top: targetY, behavior: jp_for_twentytwentyone_toc['toc_scroll']});
  };

  /**
   * アンカータグにイベントを登録
   */
  const wpadminbar = document.getElementById('wpadminbar');
  const smoothOffset = (wpadminbar ? wpadminbar.clientHeight : 0) + 2;
  const links = document.querySelectorAll('.toc-list a[href^="#"]');
  for (let i = 0; i < links.length; i++) {
    links[i].addEventListener('click', function (e) {
      const href = e.currentTarget.getAttribute('href');
      const splitHref = href.split('#');
      const targetID = splitHref[1];
      const target = document.getElementById(targetID);

      if (target) {
        e.preventDefault();
        smoothScroll(target, smoothOffset);
      } else {
        return true;
      }
      return false;
    });
  }

  /**
   * 目次項目の開閉
   */
  const tocs = document.getElementsByClassName('toc');
  for (let i = 0; i < tocs.length; i++) {
    const toggle = tocs[i].getElementsByClassName('toc-toggle')[0].getElementsByTagName('a')[0];
    toggle.addEventListener('click', function (e) {
      const target = e.currentTarget;
      const tocList = tocs[i].getElementsByClassName('toc-list')[0];
      if (tocList.hidden) {
        target.innerText = jp_for_twentytwentyone_toc['toc_close_text'];
      } else {
        target.innerText = jp_for_twentytwentyone_toc['toc_open_text'];
      }
      tocList.hidden = !tocList.hidden;
    });
  }
};

jpForTwentyTwentyOneToc();
