
const props = ["name","tel","email"];
const opts = {multiple: true};
let json_Contact_Api = [];

function contactos_verifica_soporte(){
    _('btncontactos').style = "display: none;";
    const supported = 'contacts' in navigator;
    if (supported) {
        _('divContactoManual').style = "display: none;";
        _('btnmasinvitados').style = "display: inline;";
        getContacts();
    } else {
        _('divContactoManual').style = "display: inline;";
        _('btnmasinvitados').style = "display: none;";
    }
}

function contactos_procesa_tel(telefono){
    let retorno = telefono;
    retorno = retorno.replaceAll("+","").replace(" ","").replaceAll(" ","").replaceAll("-","").replaceAll("(","").replaceAll(")","");
    if (retorno.length > 10){
        let pais = telefono.split(" ")[0].replaceAll("+","").replace(" ","").replaceAll(" ","").replaceAll("-","").replaceAll("(","").replaceAll(")","");
        retorno = pais + '' + retorno.substr(retorno.length - 10,retorno.length);
    }
    return retorno;
}

async function getContacts() {
    try {
        const contacts = await navigator.contacts.select(props, opts);
        for(let i=0;i<contacts.length;i++){
            let nombre_contacto = contacts[i].name[0];
            let telefono_contacto = (contacts[i].tel[0] === undefined)?"":contactos_procesa_tel(contacts[i].tel[0].replace("+",""));
            let correo_contacto = (contacts[i].email[0] === undefined)?"":contacts[i].email[0];
            json_Contact_Api.push({"nombre": nombre_contacto,"correo": correo_contacto,"telefono": telefono_contacto});
            agregaInvitado_contactos_api(nombre_contacto);
        }
        console.log(json_Contact_Api);
    } catch (ex) {
        // Handle any errors here.
    }
}

function agregaInvitado_contactos_api(nombre){
    let strDivDato = "<div id=\"tmp_" + Base64.encode(nombre) + "\" onclick=\"datoinvitadorem('"+Base64.encode(nombre)+"');\" style=\"padding: 8px;\"><div class=\"vue-treeselect__multi-value-item\"><span class=\"vue-treeselect__multi-value-label\">" + nombre + "</span><span class=\"vue-treeselect__icon vue-treeselect__value-remove\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 348.333 348.333\"><path d=\"M336.559 68.611L231.016 174.165l105.543 105.549c15.699 15.705 15.699 41.145 0 56.85-7.844 7.844-18.128 11.769-28.407 11.769-10.296 0-20.581-3.919-28.419-11.769L174.167 231.003 68.609 336.563c-7.843 7.844-18.128 11.769-28.416 11.769-10.285 0-20.563-3.919-28.413-11.769-15.699-15.698-15.699-41.139 0-56.85l105.54-105.549L11.774 68.611c-15.699-15.699-15.699-41.145 0-56.844 15.696-15.687 41.127-15.687 56.829 0l105.563 105.554L279.721 11.767c15.705-15.687 41.139-15.687 56.832 0 15.705 15.699 15.705 41.145.006 56.844z\"></path></svg></span></div></div>";
    $('#divinvitados').append(strDivDato);
}
