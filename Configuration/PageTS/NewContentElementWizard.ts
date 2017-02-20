mod.wizards {
    newContentElement.wizardItems {
        plugins {
            elements {
                plugins_tx_wttwitter_pi1 {
                    icon = EXT:wt_twitter/Resources/Public/Icons/ce_wiz.gif
                    title = LLL:EXT:wt_twitter/Resources/Private/Language/locallang_module.xml:list_title
                    description = LLL:EXT:wt_twitter/Resources/Private/Language/locallang_module.xml:list_plus_wiz_description
                    tt_content_defValues {
                        CType = list
                        list_type = wttwitter_list
                    }
                }
            }
        }
    }
}