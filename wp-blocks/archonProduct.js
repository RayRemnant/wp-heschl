var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
	TextControl = wp.components.TextControl,
	TextareaControl = wp.components.TextareaControl,
	InspectorControls = wp.editor.InspectorControls;
const { RichText } = wp.blockEditor;

registerBlockType("rayremnant/archon-product", {
	title: "Archon Product",

	category: "common",

	edit: (props) => {
		function updateType(e) {
			props.setAttributes({
				type: e.target.value,
			});
			//console.log(props.attributes)
		}

		function updateName(e) {
			props.setAttributes({
				name: e.target.value,
			});
			//console.log(props.attributes)
		}

		function updateText(newText) {
			props.setAttributes({
				text: newText,
			});
			//console.log(props.attributes)
		}

		function updateCollection(e) {
			//console.log(e.target.value)

			props.setAttributes({
				collection: e.target.value,
			});

			//console.log(props.attributes.collection)
		}

		async function requestData() {
			console.log(JSON.stringify(props.attributes));
			let headers = new Headers();
			headers.set("Authorization", props.attributes.serverAuth);
			headers.set("Content-Type", "application/json");

			console.log(JSON.stringify(props.attributes));

			const requestOptions = {
				headers: headers,
				method: "POST",
				body: JSON.stringify({
					textQuery: props.attributes.name,
					collection: props.attributes.collection,
				}),
			};

			data = await fetch(
				props.attributes.serverHost + "/db/search",
				requestOptions
			);

			if (!data) {
				console.log("data is empty");
				return;
			}

			data = await data.json();
			//console.log(JSON.stringify(data));
			data = data[0];

			if (!data) {
				console.log("data is empty");
				return;
			}

			//console.log(data.name)

			if (props.attributes.collection == "product") {
				props.setAttributes({
					name: (data.name.full + " " + (data.name.capacity || "")).trim(),
				});
			}

			if (props.attributes.collection == "best") {
				if (data.best.section && data.best.section.name) {
					var name = (data.best.name + " " + data.best.section.name).trim();
				}
				props.setAttributes({ name: name });
			}
		}

		return [
			el(
				"select",
				{
					type: "select",
					value: props.attributes.collection,
					onChange: updateCollection,
				},
				[
					el("option", { value: "best" }, "best"),
					el("option", { value: "product" }, "product"),
				]
			),
			el("input", {
				style: { width: "70%" },
				type: "text",
				value: props.attributes.name,
				onChange: updateName,
			}),

			el(
				"button",
				{
					style: { verticalAlign: "middle", borderRadius: "4px" },
					type: "button",
					onClick: requestData,
				},
				"Load Data"
			),

			el(RichText, {
				tagName: "p",
				value: props.attributes.text,
				onChange: updateText,
			}),
		];
	},

	save: (props) => {},
});
