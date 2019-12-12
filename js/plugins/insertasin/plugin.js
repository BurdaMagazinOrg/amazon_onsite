/**
 * CKEditor plugin for split text feature for Paragraphs text fields.
 *
 * @file plugin.js
 */

(function ($, Drupal, drupalSettings, CKEDITOR) {

  /**
   *  CKEditor.
   *
   * @param {object} editorSettings
   *   CKEditor settings object.
   */
  const registerPlugin = function (editorSettings) {
    // The insertAsin toolbar and plugin should be registered only once.
    if (editorSettings.extraPlugins.indexOf("insertAsin") !== -1) {
      return;
    }

    // We want to have plugin enabled for all text editors.
    editorSettings.extraPlugins += ",insertAsin";

    // Add insertAsin plugin as last one in toolbar and preserved
    // there after ajax requests are executed.
    const toolbar = editorSettings.toolbar;
    if (typeof editorSettings._insertasinIndex === "undefined") {
      editorSettings.insertasinIndex = toolbar.length - 1;
      toolbar.push("/");
    }

    toolbar[editorSettings.insertasinIndex] = {
      name: Drupal.t("Insert amazon product"),
      items: ["insertAsin"]
    };
  };

  /**
   * Register insertAsin plugin for all CKEditors.
   *
   * @type {{attach: attach}}
   */
  Drupal.behaviors.setinsertAsin = {
    attach() {
      if (
        !drupalSettings ||
        !drupalSettings.editor ||
        !drupalSettings.editor.formats
      ) {
        return;
      }

      $.each(drupalSettings.editor.formats, (editorId, editorInfo) => {
        if (editorInfo.editor === "ckeditor") {
          registerPlugin(editorInfo.editorSettings);
        }
      });
    }
  };

  /**
   * Register define new plugin.
   */
  CKEDITOR.plugins.addExternal(
    "insertAsin",
    `/${drupalSettings.amazon_onsite._path}/js/plugins/insertasin/`,
    "plugin.js"
  );

  CKEDITOR.plugins.add("insertAsin", {
    hidpi: true,
    icons: "insertasin",
    requires: "dialog",

    init(editor) {
      // Only add to field_content.
      if (
        !(
          editor.element
            .getAttribute("data-drupal-selector")
            .replace(/-[0-9]+-value$/, "") === "edit-field-content"
        )
      ) {
        return;
      }

      editor.addCommand(
        "insertAsinDialog",
        new CKEDITOR.dialogCommand("insertAsinDialog", {
          allowedContent: "div[data-itemtype]",
          requiredContent: "div"
        })
      );

      CKEDITOR.dialog.add("insertAsinDialog", api => ({
        title: Drupal.t("Insert amazon product card"),
        resizable: CKEDITOR.DIALOG_RESIZE_BOTH,
        minWidth: 400,
        minHeight: 200,
        onOk() {
          const asin = CKEDITOR.tools.trim(this.getValueOf("general", "asin"));
          const div = CKEDITOR.dom.element.createFromHtml(
            `<div data-itemtype="product"><a href="https://www.amazon.de/dp/${asin}">ASIN:${asin}</a></div>`
          );
          editor.insertElement(div);
        },
        contents: [
          {
            id: "general",
            label: "ASIN",
            elements: [
              {
                type: "text",
                id: "asin",
                label: "ASIN",
                validate: CKEDITOR.dialog.validate.functions(
                  val => !(val.length < 10),
                 Drupal.t( "ASIN must be 10 characters long.")
                )
              }
            ]
          }
        ]
      }));

      editor.ui.addButton("insertAsin", {
        label: Drupal.t("Insert amazon product"),
        command: "insertAsinDialog"
      });

      if (editor.addMenuItems) {
        editor.addMenuGroup("insertAsin");
        editor.addMenuItems({
          insertasin: {
            label: Drupal.t("Insert amazon product"),
            command: "insertasin",
            group: "insertasin",
            order: 1
          }
        });
      }

      if (editor.contextMenu) {
        editor.contextMenu.addListener(() => ({
          insertasin: CKEDITOR.TRISTATE_OFF
        }));
      }
    }
  });
})(jQuery, Drupal, drupalSettings, CKEDITOR);
