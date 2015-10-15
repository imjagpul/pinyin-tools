<?php 

$annotatorLink=$this->createUrl("/annotator");
//this is a hard-coded link to the Matthews system.
$matthewsLink=$this->createUrl("/system/view", array("id"=>"1"));
$heisigLink=$this->createUrl("/system/view", array("id"=>""));

?>

<p>
A "system" is a collection of mnemonics.
<p>
A mnemonic can be anything from simple etymological explanation or a sentence describing what
elements the character is composed of, to
elaborate stories employing complicated tricks. Write whatever you find useful to help you remember 
the characters.
<p>
For a very powerful system, take a look at 
<a href="<?php echo $matthewsLink; ?>">Learning Chinese Characters by Matthews</a>.
<p>
You might want to describe the logic your system is following in the description (especially if you share the system publicly). 
</p>


<h2>Target and mnemonics language</h2>
<p>
Each system is a collection of mnemonics for a particular language 
(so if you are learning Mandarin, and later decide to also learn Cantonese,
add your mnemonics for Cantonese pronunciation to a separate system - this will
ensure the correct dictionary is shown when adding entries.)
</p>

 							
<h2>Follow-up systems</h2>
<p>
You can create a system from the scratch and add all characters you want.
Or you create a follow-up system. For example, if you studied Heisig or Matthews book and want to create 
mnemonics following the same logic for characters not covered by the book, you can add a system that "inherits" 
the <a href="<?php echo $heisigLink; ?>">Heisig</a> or <a href="<?php echo $matthewsLink; ?>">Matthews</a> system, respectively.  
</p>
<p>
For every character in the <a href="<?php echo $annotatorLink; ?>">annotated</a> text, you will see the mnemonic from your own system if there is one,
or the entry from the parent system if there is none.   
</p>
<p>
When adding new entries, keywords from parent system are considered - no conflicting keywords will be suggested
for the new entries. 
</p>




