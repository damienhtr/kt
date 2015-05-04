@echo off
set FILE=gen.sql
pause
echo Generation>%FILE%
for /L %%x IN (1, 1, 9) do (
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('1', 'Devis', '0', '10%%x'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('2', 'RC', '0', '10%%x'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('3', 'RCD', '0', '10%%x'^); >> %FILE%
)
set x=10
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('1', 'Devis', '0', '1%x%'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('2', 'RC', '0', '1%x%'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('3', 'RCD', '0', '1%x%'^); >> %FILE%


for /L %%x IN (1, 1, 9) do (
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('1', 'Devis', '0', '20%%x'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('2', 'RC', '0', '20%%x'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('3', 'RCD', '0', '20%%x'^); >> %FILE%
)
for /L %%x IN (10, 1, 18) do (
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('1', 'Devis', '0', '2%%x'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('2', 'RC', '0', '2%%x'^); >> %FILE%
echo INSERT INTO `chantier_piece` ^(`reference`, `type`, `file`, `idlot`^) VALUES ^('3', 'RCD', '0', '2%%x'^); >> %FILE%
)
pause