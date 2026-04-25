# contao-sources-bundle

### Quellen verwalten unter Contao (ab 5+)
Dieses Bundle bietet eine einfache Verwaltung von Quellen für das CMS Contao. Der Begriff "Quellen" wird hier in einem relativ **weiten Sinne** verwendet und bezieht sich vor allem **literarische Quellen, Drucke, Karten, Pläne und Risse, aber auch Fotos, Websites** und andere Entitäten, auf die in Contao zitiert werden.

Dabei orientiert sich die aktuelle Version ganz grob an APA 7, was in weiten Bereichen der wissenschaftlichen Publikationen als eine Art quasi-Standard betrachtet wird. Jedoch sind nicht alle Regeln von APA 7 hier bereits implementiert. 

Zurzeit befindet sich das Bundle noch in einem sehr experimentellen Statium.

### Folgende Funktionen sind implementiert
Anhand des vom Modul am Backend neu hinzugefügten Menüs **&raquo;Quellenregister&laquo;** sollen die bisher implementierten Funktionen kurz erklärt werden.  

![img.png](docs/img.png)

### 1. Quellen   
Der Menüpunkt **&raquo;Quellen&laquo;** ermöglicht die eigentliche Verwaltung der Quellen. Dort finden Sie eine Auflistung aller von Ihnen erfassten Quellen. 
> [!NOTE] 
> Bei den Quellen handelt es sich um abhängige Dasten (abhängige Tabelle). Die hier eingefügten Daten sind teilweise von den folgenden Daten abhängig. Das bedeutet, dass Sie zuerst die unabhängigen Daten erfassen müssen, bevor Sie die eigentliche Quelle erfassen können. 
### 2. AutorInnen
Hier können zurzeit Angaben zu AutorInnen erfasst werden. Diese beschränken sich aktuell lediglich auf den Namen und alle zugehörigen Vornamen. Die Vornamen werden gemäß der APA-Regeln angepasst und formatiert.
### 3. Periodika
Unter **&raquo;Periodika&laquo;** können Sie ebendiese erfassen. Die Umsetzung ist zurzeit noch experimentell. Sie können Reihen- und Zeitschriften-Namen erfassen. Weitere Daten werden noch nicht ausgewertet.
### 4. Verlage
Verlage können aktuell in ihrer minimalen Form `Ort1; Ort2; ... OrtN : Verlagsname` erfasst werden.


