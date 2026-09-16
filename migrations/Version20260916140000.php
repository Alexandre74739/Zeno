<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Seeds the initial bank of daily quiz questions.
 */
final class Version20260916140000 extends AbstractMigration
{
    /**
     * Each entry is [prompt, choices, index of the correct choice].
     *
     * @var list<array{0: string, 1: list<string>, 2: int}>
     */
    private const QUESTIONS = [
        ['Quelle est la capitale de l\'Australie ?', ['Sydney', 'Melbourne', 'Canberra', 'Perth'], 2],
        ['Combien la Terre a-t-elle de continents ?', ['5', '6', '7', '8'], 2],
        ['Quel est le plus grand océan du monde ?', ['Atlantique', 'Indien', 'Arctique', 'Pacifique'], 3],
        ['Qui a peint la Joconde ?', ['Michel-Ange', 'Léonard de Vinci', 'Raphaël', 'Botticelli'], 1],
        ['Quel est l\'organe le plus grand du corps humain ?', ['Le foie', 'Le cœur', 'La peau', 'Le cerveau'], 2],
        ['En quelle année a eu lieu la Révolution française ?', ['1789', '1799', '1804', '1815'], 0],
        ['Quel gaz les plantes absorbent-elles principalement ?', ['Oxygène', 'Azote', 'Dioxyde de carbone', 'Hydrogène'], 2],
        ['Quelle planète est surnommée la "planète rouge" ?', ['Vénus', 'Mars', 'Jupiter', 'Saturne'], 1],
        ['Combien de cordes une guitare classique possède-t-elle ?', ['4', '5', '6', '7'], 2],
        ['Quel est le plus long fleuve du monde ?', ['Le Nil', 'L\'Amazone', 'Le Yangtsé', 'Le Mississippi'], 0],
        ['Quelle langue compte le plus de locuteurs natifs dans le monde ?', ['Anglais', 'Espagnol', 'Mandarin', 'Hindi'], 2],
        ['Quel pays a inventé le papier ?', ['L\'Égypte', 'La Chine', 'La Grèce', 'L\'Inde'], 1],
        ['Quel est le symbole chimique de l\'or ?', ['Ag', 'Fe', 'Au', 'Pb'], 2],
        ['Combien de joueurs compte une équipe de football sur le terrain ?', ['9', '10', '11', '12'], 2],
        ['Quel est le plus petit pays du monde ?', ['Monaco', 'Saint-Marin', 'Le Vatican', 'Liechtenstein'], 2],
        ['Qui a écrit "Les Misérables" ?', ['Émile Zola', 'Victor Hugo', 'Gustave Flaubert', 'Honoré de Balzac'], 1],
        ['Quelle est la monnaie officielle du Japon ?', ['Le yuan', 'Le won', 'Le yen', 'Le ringgit'], 2],
        ['Combien d\'os compte le squelette humain adulte ?', ['186', '206', '226', '246'], 1],
        ['Quel océan borde la côte ouest des États-Unis ?', ['Atlantique', 'Pacifique', 'Indien', 'Arctique'], 1],
        ['Quel est le plus haut sommet du monde ?', ['Le K2', 'Le Mont Blanc', 'L\'Everest', 'Le Kilimandjaro'], 2],
        ['Combien de côtés possède un hexagone ?', ['5', '6', '7', '8'], 1],
        ['Quelle est la plus grande planète du système solaire ?', ['Saturne', 'Jupiter', 'Neptune', 'Uranus'], 1],
        ['Qui a développé la théorie de la relativité ?', ['Isaac Newton', 'Niels Bohr', 'Albert Einstein', 'Galilée'], 2],
        ['Quelle est la capitale du Canada ?', ['Toronto', 'Vancouver', 'Ottawa', 'Montréal'], 2],
        ['Combien de temps dure une année sur Mars, environ ?', ['1 an terrestre', '687 jours terrestres', '365 jours terrestres', '100 jours terrestres'], 1],
        ['Quel est le plus grand désert chaud du monde ?', ['Le Sahara', 'Le Gobi', 'Le Kalahari', 'Le désert d\'Arabie'], 0],
        ['Qui a peint "La Nuit étoilée" ?', ['Claude Monet', 'Vincent van Gogh', 'Paul Cézanne', 'Edgar Degas'], 1],
        ['Quelle est la plus haute chute d\'eau du monde ?', ['Les chutes Victoria', 'Les chutes du Niagara', 'Le Salto Angel', 'Les chutes d\'Iguazu'], 2],
        ['Quel métal est liquide à température ambiante ?', ['Le plomb', 'Le mercure', 'L\'étain', 'Le zinc'], 1],
        ['Combien de dents possède un adulte en moyenne ?', ['28', '30', '32', '34'], 2],
        ['Quelle est la monnaie officielle du Royaume-Uni ?', ['L\'euro', 'La livre sterling', 'Le dollar', 'Le franc'], 1],
        ['Quel est le plus petit os du corps humain ?', ['Le fémur', 'L\'étrier', 'Le tibia', 'Le radius'], 1],
        ['Quelle est la capitale de l\'Égypte ?', ['Alexandrie', 'Le Caire', 'Louxor', 'Gizeh'], 1],
        ['Qui a composé "La Flûte enchantée" ?', ['Ludwig van Beethoven', 'Johann Sebastian Bach', 'Wolfgang Amadeus Mozart', 'Franz Schubert'], 2],
        ['Quel est le plus grand pays du monde par superficie ?', ['Le Canada', 'La Chine', 'Les États-Unis', 'La Russie'], 3],
        ['Combien y a-t-il de couleurs dans un arc-en-ciel ?', ['5', '6', '7', '8'], 2],
        ['Quel est le symbole chimique du sodium ?', ['So', 'Sd', 'Na', 'S'], 2],
        ['Dans quel pays se trouve la ville de Marrakech ?', ['La Tunisie', 'L\'Algérie', 'Le Maroc', 'La Libye'], 2],
        ['Quel est l\'animal terrestre le plus rapide ?', ['Le lion', 'Le guépard', 'L\'antilope', 'Le zèbre'], 1],
        ['Quelle est la plus longue muraille construite par l\'homme ?', ['La muraille de Chine', 'Le mur d\'Hadrien', 'La ligne Maginot', 'Le mur de Berlin'], 0],
        ['Quel est le plus grand lac d\'eau douce du monde ?', ['Le lac Victoria', 'Le lac Baïkal', 'Le lac Supérieur', 'Le lac Tanganyika'], 2],
        ['Qui a écrit "Roméo et Juliette" ?', ['Charles Dickens', 'William Shakespeare', 'Oscar Wilde', 'Jane Austen'], 1],
        ['Combien de temps met la lumière du Soleil pour atteindre la Terre, environ ?', ['8 secondes', '8 minutes', '8 heures', '8 jours'], 1],
        ['Quelle est la capitale de l\'Espagne ?', ['Barcelone', 'Séville', 'Madrid', 'Valence'], 2],
        ['Quel instrument sert à mesurer la pression atmosphérique ?', ['Le thermomètre', 'Le baromètre', 'L\'hygromètre', 'L\'anémomètre'], 1],
        ['Quel est le plus grand mammifère du monde ?', ['L\'éléphant d\'Afrique', 'La baleine bleue', 'Le rorqual commun', 'Le cachalot'], 1],
        ['En quelle année l\'homme a-t-il marché sur la Lune pour la première fois ?', ['1965', '1969', '1972', '1975'], 1],
        ['Quelle est la capitale de la Grèce ?', ['Thessalonique', 'Athènes', 'Sparte', 'Corinthe'], 1],
        ['Quel est le plus grand organe interne du corps humain ?', ['Le cœur', 'Le foie', 'Le poumon', 'Le rein'], 1],
    ];

    public function getDescription(): string
    {
        return 'Seed the initial bank of daily quiz questions.';
    }

    public function up(Schema $schema): void
    {
        foreach (self::QUESTIONS as [$prompt, $choices, $correctIndex]) {
            $this->connection->executeStatement(
                'INSERT INTO question (prompt) VALUES (?)',
                [$prompt],
            );
            $questionId = (int) $this->connection->lastInsertId();

            foreach ($choices as $index => $label) {
                $this->connection->executeStatement(
                    'INSERT INTO question_choice (label, correct, question_id) VALUES (?, ?, ?)',
                    [$label, $index === $correctIndex ? 1 : 0, $questionId],
                );
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM question_choice');
        $this->addSql('DELETE FROM question');
    }
}
