function openTab(event,tab)
{
    let content=document.querySelectorAll(".tab-content")
    content.forEach(content=> content.classList.remove("active"))

    let button=document.querySelectorAll(".tab-button")
    button.forEach(button=>button.classList.remove("active"))

    document.getElementById(tab).classList.add("active")

    event.currentTarget.classList.add("active")
}