registerBlockType( 'rayremnant/archon-notes', {
  title: 'Archon Notes',

  category: 'common',
  
  edit: (props) => {
    
    function updateText( newText ) {
        props.setAttributes( { 
            text: newText
        })
    }
    
    function updateTitle( newTitle ) {
        props.setAttributes( { 
            title: newTitle
        })
    }

    return [
        
        el("header", {type: "text", class:"has-text-align-center"}, 
            props.attributes.header,
            el(RichText, {tagName: 'span', value: props.attributes.title, onChange: updateTitle})
        ),

    	el(RichText, {tagName: 'p', value: props.attributes.text, onChange: updateText}),
        
    ]
    },
  
  save: () => {
    //resolved server side
  }
});