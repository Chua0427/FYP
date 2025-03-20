function openfilter() {
    let sidebar = document.querySelector(".sidebar");


    if(sidebar.style.opacity === "0" || sidebar.style.opacity === "")
    {
        sidebar.style.opacity = "1";
        sidebar.style.visibility= "visible";
        sidebar.style.width = "250px";
        sidebar.style.padding = "10px";
    }
    else
    {
        sidebar.style.opacity = "0";
        sidebar.style.visibility = "hidden";
        sidebar.style.width = "0";
        sidebar.style.padding = "0";
    }
}
