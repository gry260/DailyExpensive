<?php
/* passwordValidator.php      written : 7/12/10     by: John Lin

This program checks to make sure the password passes the campus' minimum password 
complexity standards (https://security.berkeley.edu/MinStds/Passwords.html). It 
returns an array of error messages (an empty array means there are no errors).

This program takes in an options array that can change the following settings:
minPassSize - minimum password size (default is 8)
maxPassSize - maximum password size (default is 15)
minCharSet - minimum character sets the password must contain (default is 2)
allowedSymbols - an array of symbols allowed in the password (default allowed 
	characters are !@#$%_,)

> $newOptions = Array('minPassSize'=>'5', 'maxPassSize'=>'7', 'minCharSet'=>'3',
> 	'allowedSymbols'=>array('!','@','#','$','%'));
> $validator = new Password($newOptions);
> $validator->validatePassword($password, $username);

Below is the default password complexity guidelines set by the campus: 

The password MUST: 
Contain eight characters or more 
Contain characters from two of the following three character classes: 
Alphabetic (e.g., a-z, A-Z) 
Numeric (i.e. 0-9) 
Punctuation and other characters 

The password MUST NOT be:
A derivative of the username 
A word found in a dictionary (English or foreign) 
A dictionary-word spelled backwards 
A dictionary-word (forward or backwards) preceded and/or followed by any other 
single character (e.g., secret1, 1secret, secret?, secret!)

Copyright 2010. The Regents of the University of California (Regents). All
Rights Reserved.

IN NO EVENT SHALL REGENTS BE LIABLE TO ANY PARTY FOR DIRECT, INDIRECT, SPECIAL,
INCIDENTAL, OR CONSEQUENTIAL DAMAGES, INCLUDING LOST PROFITS, ARISING OUT OF THE
USE OF THIS SOFTWARE AND ITS DOCUMENTATION, EVEN IF REGENTS HAS BEEN ADVISED OF
THE POSSIBILITY OF SUCH DAMAGE.

REGENTS SPECIFICALLY DISCLAIMS ANY WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.
THE SOFTWARE AND ACCOMPANYING DOCUMENTATION, IF ANY, PROVIDED HEREUNDER IS
PROVIDED "AS IS". REGENTS HAS NO OBLIGATION TO PROVIDE MAINTENANCE, SUPPORT,
UPDATES, ENHANCEMENTS, OR MODIFICATIONS.
*/

class Password
{
  public function __construct($options = array())
	{
		/* set minimum password size */
		if (isset($options['minPassSize']))
			$this->minPassSize = $options['minPassSize'];
		else
			$this->minPassSize = 8;
			
		/* set maximum password size */
		if (isset($options['maxPassSize']))
			$this->maxPassSize = $options['maxPassSize'];
		else
			$this->maxPassSize = 15;
		
		/* set minimum char set required in password */
		if (isset($options['minCharSet']))
			$this->minCharSet = $options['minCharSet'];
		else
			$this->minCharSet = 2;
			
		/* set allowed symbols */
		$this->allowedSymbols = array ('!','@','#','$','%','_','&',',');
		if (isset($options['allowedSymbols']))
		{
			if (is_array($options['allowedSymbols']))
				$this->allowedSymbols = $options['allowedSymbols'];
		}
		
		/* set minPassSize = maxPassSize if min is > max */
		if ($this->minPassSize > $this->maxPassSize)
			$this->minPassSize = $this->maxPassSize;
	}
	
	public function validatePassword($strPassword, $strUsername = NULL)
	{
		$this->errors = array();
		
		/* warn if password is less than minPassSize */
		if (strlen($strPassword) < $this->minPassSize)
			$this->errors[] = 'Password has to be more than '.$this->minPassSize.
				' characters long.';

		/* warn if password is greater than maxPassSize */
		if (strlen($strPassword) > $this->maxPassSize)
			$this->errors[] = 'Password has to be less than or equal to '.
				$this->maxPassSize.' characters long.';

		/* warn if password contains not allowed characters */
		$symbols = preg_replace('/([a-zA-Z0-9]*)/', '', $strPassword);
		for ($i = 0; $i < strlen($symbols); $i++)
		{
			if (!in_array($symbols[$i], $this->allowedSymbols))
				$this->errors[] = $symbols[$i].' is not allowed in the password.';
		}

		/* warn if password contains only one character set (need at least two) */
		if($this->getCharSetCount($strPassword) < 2)
		{
			$symbols = '';
			foreach($this->allowedSymbols as $value)
				$symbols .= $value;

			$this->errors[] = 'Password must contain characters from two of the 
				following three character classes: <br> 
				Alphabetic (e.g., a-z, A-Z) <br> 
				Numeric (i.e. 0-9) <br> 
				Punctuation and/or other characters ('.$symbols.')';		
		}
		
		/* warn if password contains username */
		if (isset($strUsername) && !empty($strUsername))
		{
			if (stristr($strPassword, $strUsername) != false)
				$this->errors[] = 'Password must not contain username.';
		}

		/* if password do not contain non alpha-numeric characters, then check if 
			it is a dictionary-word */
		if (!preg_match('/([^a-zA-Z0-9]+)/', $strPassword))
		{
			/* warn if password is a dictionary-word */
			if ($this->valWord($strPassword))
				$this->errors[] = 'Password must not be a dictionary-word.';

			/* warn if password is a dictionary-word spelled backward */
			if ($this->valWord(strrev($strPassword)))
				$this->errors[] = 'Password must not be a dictionary-word 
					spelled backward.';
		}
		
		/* warn if password is an dictionary-word (forward or backwards) preceded 
		and/or followed by any other single character (e.g., secret1, 1secret, 
		secret?, secret!) */
		$trimmedPassword = $this->trimFirstLast($strPassword);
		if (strlen($trimmedPassword) != strlen($strPassword))
		{
			/* only check if trimmed password is a dictionary-word, if it does not 
				contain non-alpha-numeric characters */
			if (!preg_match('/([^a-zA-Z0-9]+)/', $trimmedPassword))
			{
				/* warn if password is a dictionary-word preceded and/or followed by any 
					other single character */
				if ($this->valWord($trimmedPassword) || 
					$this->valWord(strrev($trimmedPassword)))
					$this->errors[] = 'Password must not be a dictionary-word 
					(forward or backwards) preceded and/or followed by any other single 
					character (e.g., secret1, 1secret, secret?, secret!).';
			}
		}
		
		return $this->errors;
	}
	
	/* count character sets the word contains */
	protected function getCharSetCount($strWord)
	{
		$charSetCount = 0;
		
		/* alpha character set */
		if (preg_match('/([a-zA-Z]+)/', $strWord))
			$charSetCount++;

		/* numeric character set */
		if (preg_match('/([0-9]+)/', $strWord))
			$charSetCount++;
			
		/* allowed symbols set */
		$symbols = '';
		
		foreach($this->allowedSymbols as $value)
			$symbols .= $value;
			
		if (preg_match('/(['.$symbols.']+)/', $strWord))
			$charSetCount++;
			
		return $charSetCount;

	}

	/* this function strips a single character, of the different char set, from 
		the begenning and/or the end of the password */
	protected function trimFirstLast($strWord)
	{
		$trimmedString = $strWord;
		$wordSize = strlen($trimmedString);

		/* only process if word is more than 4 characters long */
		if ($wordSize >= 4)
		{
			/* check first character */
			$firstType = $this->getCharType(substr($trimmedString, 0, 1));
			$secondType = $this->getCharType(substr($trimmedString, 1, 1));
			/* trim, if first and second chars are of different type */
			if ($firstType != $secondType)
				$trimmedString = substr($trimmedString, 1, $wordSize--);

			/* check last character */
			$lastTwoChars = substr($trimmedString, -2);
			$lastType = $this->getCharType(substr($lastTwoChars, -1));
			$secondToLastType = $this->getCharType(substr($lastTwoChars, 0, 1));
			/* trim, if last and second to last chars are of different type */
			if ($lastType != $secondToLastType)
				$trimmedString = substr($trimmedString, 0, -1);

		}
		
		return $trimmedString;
	}
	
	/* this function returns character type of a character */
	protected function getCharType($char) 
	{
		if (preg_match('/([a-zA-Z])/', $char))
			$type = 'alpha';
		elseif (preg_match('/([0-9])/', $char))
			$type = 'numeric';
		else
			$type = 'other';

		return $type;
	}

	/* check to see if a string is a dictionary-word */
	protected function valWord($strWord)
	{
		if (strlen($strWord) == 0)
			return false;
		else
		{
			$encodedWord = urlencode($strWord);
			$url = 'http://www.onelook.com/?w='.$encodedWord.'&xml=1';
			$remoteXML = file_get_contents($url);
			$xml = simplexml_load_string($remoteXML);
			if(!xml)
				$definitionFound = false;
			elseif(isset($xml->OLQuickDef))
				$definitionFound = true;
			else
				$definitionFound = false;
				
			return $definitionFound;
		}
	}
}

?>