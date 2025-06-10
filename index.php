<!DOCTYPE html>
<html lang="pt" style="margin: 0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Editor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/theme/dracula.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/dialog/dialog.css">
</head>
<body style="margin: 0">
    
<div class="box-header">
    <input type="file" id="fileInput" accept=".html">
    <div class="box-header">
        <div contenteditable="true" id="htmlTitle">[sem titulo]</div>
    </div>
    <div style="display: flex;gap: 16px;">
        <button id="addMetaBtn">Add Meta Tags</button>
        <button id="downloadBtn">Download HTML</button>
    </div>
</div>
<div class="container-editor">
    <div class="tools-header">
        <p>Habilitar Edição Visual</p>
        <label class="switch">
          <input type="checkbox" id="toggleEdit">
          <span class="slider round"></span>
        </label>

        <div id="options-tools-header" style="display: none;">
            <a id="adicionarLink" href="#">Add Link</a>
        </div>
    </div>
        
    <div id="split-container">
        <div id="preview">
            <div></div>
        </div>
        <div id="code">
            <div class="box-updateCodeBtn">
                <button id="updateCodeBtn" style="display: none;">Clique para Atualizar o Código</button>
            </div>
            <div id="codeInput"></div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/xml/xml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/javascript/javascript.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/css/css.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/edit/closetag.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/search/searchcursor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/search/search.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/dialog/dialog.js"></script>
<script src="https://cdn.jsdelivr.net/npm/split.js/dist/split.min.js"></script>

<script>
document.getElementById('downloadBtn').addEventListener('click', function() {
    const htmlContent = document.getElementById('preview').innerHTML;
    const blob = new Blob([htmlContent], {type: 'text/html'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = textoParaArquivo(document.getElementById('htmlTitle').innerText) + '.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});

document.getElementById('toggleEdit').addEventListener('change', function() {
  var preview = document.getElementById('preview');
  var optionsToolsHeader = document.getElementById('options-tools-header');
  
  if (this.checked) {
    // Se o switch estiver ativado
    preview.setAttribute('contenteditable', 'true');
    optionsToolsHeader.style.display = 'block'; // Exibe #options-tools-header
    alert("Ao ativar a edição visual do conteúdo, pode ser que algumas metatags sejam perdidas no processo de atualização. Se usar metatags em seu HTML, salve-as em local seguro, depois adicione manualmente no código.");
  } else {
    // Se o switch estiver desativado
    preview.removeAttribute('contenteditable');
    optionsToolsHeader.style.display = 'none'; // Oculta #options-tools-header
  }
});



// Novo código para lidar com a substituição de imagens
document.getElementById('preview').addEventListener('click', function(event) {
    if (event.target.tagName === 'IMG' && document.getElementById('toggleEdit').checked) { // Verifica se o elemento clicado é uma imagem
        const newSrc = prompt('Insira o novo link para a imagem:', event.target.src);
        if (newSrc) { // Se um novo link foi fornecido, atualiza o src da imagem
            event.target.src = newSrc;
            updateCodeBtn.style.display = 'block'; // Mostra o botão
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
  // Inicializa o CodeMirror no elemento com ID 'codeInput'
  var editor = CodeMirror(document.getElementById('codeInput'), {
    mode: "htmlmixed", // Modo que combina HTML, CSS, e JavaScript
    theme: "dracula", // Tema do editor
    lineNumbers: true, // Mostrar números de linha
    autoCloseTags: true, // Fechamento automático de tags HTML
    autoCloseBrackets: true, // Fechamento automático de colchetes
    matchBrackets: true, // Destaque de colchetes correspondentes
    lineWrapping: true, // Quebra de linha automática
  });

  var updateCodeBtn = document.getElementById('updateCodeBtn');
  var preview = document.getElementById('preview');

  // Atualiza o preview em tempo real
  editor.on('change', function() {
    preview.innerHTML = editor.getValue();
  });
  
  preview.addEventListener('input', function() {
    updateCodeBtn.style.display = 'block'; // Mostra o botão
  });
  
  updateCodeBtn.addEventListener('click', function() {
    editor.setValue(preview.innerHTML); // Atualiza o CodeMirror
    updateCodeBtn.style.display = 'none'; // Esconde o botão após a atualização
  });
  
  document.getElementById('fileInput').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (!file) {
        return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        var contents = e.target.result;
        editor.setValue(contents);

        // Extrai o título do documento HTML carregado
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = contents;
        var title = tempDiv.querySelector('title') ? tempDiv.querySelector('title').innerText : '[sem titulo]';
        
        // Mostra o título no elemento editável
        var htmlTitle = document.getElementById('htmlTitle');
        htmlTitle.innerText = title;
    };
    reader.readAsText(file);
  });
  
    document.getElementById('htmlTitle').addEventListener('input', function() {
        var newTitle = this.innerText;
        var currentValue = editor.getValue();
        var updatedValue = currentValue.replace(/<title>.*<\/title>/, `<title>${newTitle}</title>`);
        editor.setValue(updatedValue);
    });

  Split(['#preview', '#code'], {
    sizes: [50, 50], // Distribuição inicial das colunas em porcentagem
    minSize: 100, // Tamanho mínimo de cada painel em pixels
    gutterSize: 8, // Tamanho da linha de divisão que você pode arrastar para ajustar
    cursor: 'col-resize', // Tipo do cursor ao mover a linha de divisão
  });
  
    document.getElementById('addMetaBtn').addEventListener('click', function() {
        // Define as tags individuais
        const titleTag = `<title>Titulo da página</title>\n`;
        const metaContentTypeTag = `<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">\n`;
        const metaViewportTag = `<meta name="viewport" content="width=device-width, initial-scale=1.0">\n`;
    
        // Obtém o valor atual do editor
        let currentValue = editor.getValue();
        let tagsToInsert = "";
    
        // Verifica e adiciona a tag <title> se não estiver presente
        if (!currentValue.includes('<title>')) {
            tagsToInsert += titleTag;
        }
    
        // Verifica e adiciona a tag <meta http-equiv="Content-Type"> se não estiver presente
        if (!currentValue.toLowerCase().includes('http-equiv="content-type"')) {
            tagsToInsert += metaContentTypeTag;
        }
    
        // Verifica e adiciona a tag <meta name="viewport"> se não estiver presente
        if (!currentValue.toLowerCase().includes('name="viewport"')) {
            tagsToInsert += metaViewportTag;
        }
    
        // Insere as tags necessárias no início do documento HTML
        if (tagsToInsert.length > 0) {
            editor.setValue(tagsToInsert + currentValue);
        } else {
            alert("Todas as tags necessárias já estão presentes no documento.");
        }
    });

});

document.addEventListener('DOMContentLoaded', function() {
    const adicionarLink = document.getElementById('adicionarLink');
    const contentEditableDiv = document.getElementById('preview');
    let currentElement = null;

    adicionarLink.addEventListener('click', function(e) {
        console.log('click');
        e.preventDefault();
        // Ativa o modo de seleção
        contentEditableDiv.style.cursor = 'pointer';
        contentEditableDiv.addEventListener('mouseover', highlightElement, true);
        contentEditableDiv.addEventListener('click', selectElement, true);
    });

    function highlightElement(e) {
        if (currentElement) {
            currentElement.style.outline = ''; // Remove o destaque do elemento anterior
        }
        if (e.target !== contentEditableDiv) {
            currentElement = e.target;
            currentElement.style.outline = '2px dashed blue'; // Destaca o novo elemento
        }
    }

    function selectElement(e) {
        e.preventDefault();
        if (e.target !== contentEditableDiv) {
            const url = prompt('Digite o URL do link:', e.target.href);
            if (url) {
                e.target.href = url; // Adiciona o URL ao atributo href do elemento selecionado
                updateCodeBtn.style.display = 'block'; // Mostra o botão
            }
            e.target.style.outline = ''; // Remove o destaque
            // Limpa os event listeners para sair do modo de seleção
            contentEditableDiv.removeEventListener('mouseover', highlightElement, true);
            contentEditableDiv.removeEventListener('click', selectElement, true);
            contentEditableDiv.style.cursor = 'text'; // Retorna o cursor ao normal
        }
    }
});



function textoParaArquivo(texto) {
    const mapaAcentos = {
        'á': 'a', 'à': 'a', 'ã': 'a', 'â': 'a', 'ä': 'a',
        'é': 'e', 'è': 'e', 'ê': 'e', 'ë': 'e',
        'í': 'i', 'ì': 'i', 'î': 'i', 'ï': 'i',
        'ó': 'o', 'ò': 'o', 'õ': 'o', 'ô': 'o', 'ö': 'o',
        'ú': 'u', 'ù': 'u', 'û': 'u', 'ü': 'u',
        'ç': 'c', 'ñ': 'n',
        'Á': 'A', 'À': 'A', 'Ã': 'A', 'Â': 'A', 'Ä': 'A',
        'É': 'E', 'È': 'E', 'Ê': 'E', 'Ë': 'E',
        'Í': 'I', 'Ì': 'I', 'Î': 'I', 'Ï': 'I',
        'Ó': 'O', 'Ò': 'O', 'Õ': 'O', 'Ô': 'O', 'Ö': 'O',
        'Ú': 'U', 'Ù': 'U', 'Û': 'U', 'Ü': 'U',
        'Ç': 'C', 'Ñ': 'N'
    };

    texto = texto.split('').map(letra => mapaAcentos[letra] || letra).join('');
    texto = texto.toLowerCase();
    texto = texto = texto.replace(/[\s\/]+/g, '-').replace(/[^a-z0-9-]/g, '').replace(/-+/g, '-');

    return texto;
}
</script>

<style>
.container-editor .tools-header,
.container-editor #split-container,
.CodeMirror {
    height: calc(100vh - 84px);
}

.container-editor .tools-header,
.container-editor #split-container {
    display: flex;
}

.container-editor #split-container {
    margin-left: 76px;
}

.CodeMirror * {
    font-size: 14px;
}

#preview {
    height: calc(100vh - 84px);
    overflow-y: scroll;
}

#code {
    margin: 0;
    padding: 0;
    position: relative;
}

#updateCodeBtn {
    position: absolute;
    right: 0;
    left: 0;
    top: 0;
    bottom: 0;
    background: #000000c4;
    color: #ffff;
    font-size: 20px;
    z-index: 9;
}

.gutter.gutter-horizontal {
    background: #ff8cba;
    cursor: col-resize;
}

.box-header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    background: #f1f1f1;
    padding: 16px;
    align-items: center;
}

.box-header #downloadBtn {
    background: #0001ff;
    color: #fff;
    border: 0;
    padding: 14px 32px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
}

.box-header #addMetaBtn {
    background: #fff;
    color: #222 !important;
    border: 0;
    padding: 14px 32px;
    font-size: 16px;
    border-radius: 8px;
    text-decoration: none;
    font-family: arial;
    cursor: pointer;
}

.box-header #fileInput {
    background: #ffffff;
    color: #222;
    border: 0;
    padding: 14px 32px;
    font-size: 16px;
    border-radius: 8px;
}

.box-header #htmlTitle {
    font-family: arial;
}

.tools-header * {
    font-family: arial;
}

.tools-header > p {
    font-size: 14px;
    margin: 0;
    padding: 0;
}

.tools-header {
    position: absolute;
    display: flex !important;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    background: #f1f1f1;
    padding: 0 8px;
    justify-content: center;
    width: 60px !important;
}

.tools-header #adicionarLink {
    background: #fff;
    color: #222 !important;
    border: 0;
    padding: 14px 32px;
    font-size: 16px;
    border-radius: 8px;
    text-decoration: none;
    font-family: arial;
    cursor: pointer;
}

.tools-header #options-tools-header * {
    display: flex;
    flex-direction: column;
}


/* Estilo do Switch */
.tools-header .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.tools-header .switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.tools-header .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.tools-header .slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

.tools-header input:checked + .slider {
  background-color: #0001ff;
}

.tools-header input:focus + .slider {
  box-shadow: 0 0 1px #0001ff;
}

.tools-header input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Formato arredondado sliders */
.tools-header .slider.round {
  border-radius: 34px;
}

.tools-header .slider.round:before {
  border-radius: 50%;
}

</style>

</body>
</html>
