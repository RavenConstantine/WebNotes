<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <title>Заметки</title>
        <link href="/css/webix.css" rel=" stylesheet" type="text/css">
        <link href="/css/main.css" rel=" stylesheet" type="text/css">
        <script src="/js/webix.js"></script>
    </head>
    <body>
        <script>
            //Переменные
            var selectedId;
            var displayMode = "note";
            var autoSave = "{{ env('AUTO_SAVE') }}";

            //Функции
            function LoadNotesData(){
                $$("MainListNotes").load("api/note/load","json", null, true);
            }
            function CreateNote(){
                webix.ajax().get('api/note/create');
                LoadNotesData();
            }
            function OpenNote(id){
                webix.ajax().get('api/note/open/' + id).then(function(data){
                    data = data.json();
                    $$("NoteTitleEditor").setValue(data.title);
                    $$("NoteTextEditor").setValue(data.text);
                    $$("NoteTitleEditor").enable();
                    $$("NoteTextEditor").enable();
                });
                GetNoteStoryCount(id);
            }
            function SaveNote(id, title, text){
                if(title === "" && text === ""){webix.alert("Нельзя сохранить пустую заметку","alert-warning");}
                else{
                    if(title === ""){title = text;}
                    else if(text === ""){text = title;}
                    
                    webix.ajax().get('api/note/save/' + id + '/' + window.btoa(unescape(encodeURIComponent(title))) + '/' + window.btoa(unescape(encodeURIComponent(text))));
                    LoadNotesData();
                }
            }
            function DeleteNote(id){
                webix.confirm("Удалить выбранную заметку?", "confirm-warning", function(result){
                    if (result){
                        $$("top_menu").disableItem('delete');
                        $$("top_menu").disableItem('save');
                        $$("top_menu").disableItem('show_story');

                        $$("NoteTitleEditor").disable();
                        $$("NoteTextEditor").disable();
                        $$("NoteTitleEditor").setValue("");
                        $$("NoteTextEditor").setValue("");
                        webix.ajax().get('api/note/delete/' + id);
                        LoadNotesData();
                    }
                });
            }

            
            function GetNoteStoryCount(id){
                webix.ajax().get('api/note/story/getcount/' + id).then(function(data){
                    data = data.text();
                    $$("top_menu").getMenuItem("show_story").badge = data;
                });
            }
            function LoadNoteStory(id){
                $$("top_menu").disableItem('add');
                $$("top_menu").disableItem('delete');
                $$("top_menu").disableItem('save');

                $$("NoteTitleEditor").disable();
                $$("NoteTextEditor").disable();
                $$("MainListNotes").load("api/note/story/load/"+id,"json", null, true);
            }
            function OpenNoteStory(id, date){
                webix.ajax().get('api/note/story/open/' + id + '/' + date).then(function(data){
                    data = data.json();

                    $$("NoteTitleEditor").setValue(data.title);
                    $$("NoteTextEditor").setValue(data.text);
                });
            }

            function ChangeLogLenght(){
                webix.prompt("Введите новое значение",  "prompt-warning", function(result) {
                    if(result!==false){webix.ajax().get('api/settings/loglenght/' + result);}
                });
            }
            function ChangeAutoSave(){
                webix.confirm("Включить автоматическое сохранение?",  "confirm-warning", function(result) {
                    if(result){result=1}
                    else{result=0}
                    autoSave = result;
                    webix.ajax().get('api/settings/autosave/' + result);
                });
            }
            //UI
            webix.ready(function(){
                webix.ui({
                    rows:[
                        {
                            view:"menu",
                            id:"top_menu",
                            subMenuPos:"left",
                            autowidth: true,
                            layout:"x",
                            data:[
                                { 
                                    id:"add",
                                    value:"Добавить"
                                },
                                { 
                                    id:"delete",
                                    value:"Удалить", 
                                    disabled:true
                                },
                                { 
                                    id:"save",
                                    value:"Сохранить", 
                                    disabled:true
                                },
                                { 
                                    value:"Настройки...",
                                    submenu:[ 
                                        {
                                            id:"change_log_lenght",
                                            value:"Изменить размер лог файла"
                                        },
                                        {
                                            id:"change_auto_save",
                                            value:"Автосохранение"
                                        },
                                        {
                                            id:"show_story",
                                            value:"Показать историю",
                                            badge: 0, 
                                            disabled:true
                                        },
                                        {
                                            id:"show_notes",
                                            value:"Показать заметки"
                                        }
                                    ]
                                }
                            ],
                            on:{
                                onMenuItemClick:function(id){
                                    if(id==="add"){
                                        CreateNote();
                                    }
                                    else if(id==="delete"){
                                        DeleteNote(selectedId);
                                    }
                                    else if(id==="save"){
                                        SaveNote(selectedId, $$("NoteTitleEditor").getValue(), $$("NoteTextEditor").getValue());
                                    }
                                    else if(id==="change_log_lenght"){
                                        ChangeLogLenght();
                                    }
                                    else if(id==="change_auto_save"){
                                        ChangeAutoSave();
                                    }
                                    else if(id==="show_story"){
                                        displayMode="story";
                                        LoadNoteStory(selectedId);
                                    }
                                    else if(id==="show_notes"){
                                        displayMode="note";
                                        LoadNotesData(selectedId);
                                        $$("top_menu").enableItem('add');
                                    }
                                }
                            }
                        },
                        {
                            view:"dataview",
                            id:"MainListNotes",
                            template:"<div class='NoteBody'><div class='NoteTitle'>#title#</div> <div class='NoteText'>#text#</div></div>",
                            data:[],
                            xCount:2,
                            yCount:3,
                            select: 1,
                            type:{
                                height: 80
                            },
                            ready: function(){LoadNotesData();}
                        },
                        {
                            view:"text",
                            placeholder:"Заголовок",
                            id:"NoteTitleEditor",
                            keyPressTimeout:1300,
                            disabled:true
                        },
                        {
                            view:"textarea",
                            placeholder:"Текст",
                            id:"NoteTextEditor", 
                            keyPressTimeout:1300,
                            disabled:true
                        }
                    ]
                })
                //События
                $$('MainListNotes').attachEvent('onAfterSelect', function(){
                    if (displayMode === "note"){
                        selectedId=$$("MainListNotes").getSelectedId();
                        OpenNote(selectedId);
                        $$("top_menu").enableItem('delete');
                        $$("top_menu").enableItem('save');
                        $$("top_menu").enableItem('show_story');
                    }
                    else if (displayMode === "story"){
                        OpenNoteStory(selectedId, $$("MainListNotes").getSelectedId());
                    }
                });
                $$('NoteTitleEditor').attachEvent("onTimedKeyPress", function(){
                    if(autoSave == "1"){SaveNote(selectedId, $$("NoteTitleEditor").getValue(), $$("NoteTextEditor").getValue())}
                        
                });
                $$('NoteTextEditor').attachEvent("onTimedKeyPress", function(){
                    if(autoSave =="1"){SaveNote(selectedId, $$("NoteTitleEditor").getValue(), $$("NoteTextEditor").getValue())}
                });
                webix.extend($$("MainListNotes"), webix.ProgressBar);
            });
        </script>
    </body>
</html>