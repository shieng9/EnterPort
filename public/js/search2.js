const comment_area = document.getElementsByClassName("comment_area");
const words_search = document.getElementById("words_search");

words_search.addEventListener("input", () => {
  reset();
  const word = words_search.value;
  if (word === "") {
    return;
  }
  [...comment_area].forEach((part) => {
    if (part.textContent.indexOf(word) === -1) {
      part.classList.add("hide");
    }
  });
});

function reset() {
  [...document.getElementsByClassName("hide")].forEach((el) => {
    el.classList.remove("hide");
  });
}
