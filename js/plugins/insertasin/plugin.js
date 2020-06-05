/**
 * CKEditor plugin for split text feature for Paragraphs text fields.
 *
 * @file plugin.js
 */

(function ($, Drupal, CKEDITOR) {

  CKEDITOR.plugins.add("insertasin", {
    hidpi: true,
    icons: "insertasin",
    requires: "dialog",

    init: function init(editor) {

      editor.addCommand(
        "insertAsinDialog",
        new CKEDITOR.dialogCommand("insertAsinDialog", {
          allowedContent: "a[data-amazon-onsite-product]",
          allowedContent: {
            a: {
              attributes: {
                '!data-amazon-onsite-product': true
              },
              classes: {}
            }
          },
          requiredContent: new CKEDITOR.style({
            element: 'a',
            attributes: {
              'data-amazon-onsite-product': '',
            },
          }),
          modes: { wysiwyg: 1 }
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
            `<a href="https://www.amazon.de/dp/${asin}" data-amazon-onsite-product>ASIN:${asin}</a>`
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
      if (editor.ui.addButton) {
        editor.ui.addButton("InsertAsin", {
          label: Drupal.t("Insert amazon product"),
          command: "insertAsinDialog"
        });
      }

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
})(jQuery, Drupal, CKEDITOR);
